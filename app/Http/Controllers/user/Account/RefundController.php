<?php

namespace App\Http\Controllers\user\Account;


use App\Models\DanhMuc;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\LichSuDonHang;
use App\Models\NguoiDung;
use App\Models\YeuCauHoanTien;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RefundController extends Controller
{
    // Số ngày được yêu cầu hoàn tiền kể từ lúc giao thành công
    private const REFUND_DAYS = 7;

    // Số ảnh minh chứng tối đa
    private const MAX_IMAGES = 5;


    /**
     * Trang danh sách yêu cầu hoàn tiền
     * + popup tạo yêu cầu
     */
    public function index(Request $request)
    {
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $userId = (int) session('NguoiDungID');


        // ========================================
        // LẤY DANH SÁCH YÊU CẦU HOÀN TIỀN
        // ========================================

        $requests = YeuCauHoanTien::query()->from('YeuCauHoanTien as yc')
            ->join(
                'DonHang as dh',
                'dh.DonHangID',
                '=',
                'yc.DonHangID'
            )
            ->where(
                'yc.NguoiDungID',
                $userId
            )
            ->orderByDesc(
                'yc.YeuCauHoanTienID'
            )
            ->select(
    'yc.YeuCauHoanTienID',
    'yc.MoTa',
    'yc.AnhMinhChung',
    'yc.VideoMinhChung',
    'yc.TrangThai',
    'yc.GhiChuXuLy',
    'yc.NgayYeuCau',
    'dh.MaDonHang'
)
->get()
->map(function ($r) {

    $r->MaYeuCau = 'YC' . str_pad(
        $r->YeuCauHoanTienID,
        3,
        '0',
        STR_PAD_LEFT
    );

    // Chuyển JSON ảnh thành Collection
    $r->Anh = collect(
        json_decode($r->AnhMinhChung, true) ?: []
    );

    return $r;
});


        // ========================================
        // LẤY CÁC ĐƠN ĐỦ ĐIỀU KIỆN HOÀN TIỀN
        // ========================================

        $eligibleOrders = $this->eligibleOrders($userId);


        // ========================================
        // ĐƠN ĐƯỢC CHỌN SẴN
        // Khi đi từ trang Đơn hàng của tôi
        // ========================================

        $preselect = $request->query('order');

        $notice = null;

        if (
            $preselect &&
            !$eligibleOrders->contains(
                'DonHangID',
                (int) $preselect
            )
        ) {
            $notice =
                'Đơn hàng này không thể yêu cầu hoàn tiền ' .
                '(đã có yêu cầu, chưa giao hoặc quá ' .
                self::REFUND_DAYS .
                ' ngày kể từ lúc giao).';

            $preselect = null;
        }


        // ========================================
        // DỮ LIỆU CHO SIDEBAR / NAVBAR
        // ========================================

        $user = NguoiDung::query()->from('NguoiDung')
            ->where(
                'NguoiDungID',
                $userId
            )
            ->first();

        $danhMucs = DanhMuc::query()->get();


        // ========================================
        // TRẢ VỀ VIEW
        // ========================================

        return view(
            'user.account.refund',
            [
                'requests' => $requests,

                'eligibleOrders' => $eligibleOrders,

                'preselect' => $preselect,

                'notice' => $notice,

                'refundDays' => self::REFUND_DAYS,

                'user' => $user,

                'danhMucs' => $danhMucs,
            ]
        );
    }


    /**
     * Lưu yêu cầu hoàn tiền
     */
    public function store(Request $request)
    {
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $userId = (int) session('NguoiDungID');


        // ========================================
        // VALIDATE
        // ========================================

        $data = $request->validate(
            [
                'DonHangID' => [
                    'required',
                    'integer',
                ],

                'MoTa' => [
                    'required',
                    'string',
                    'max:1000',
                ],

                'AnhMinhChung' => [
                    'required',
                    'array',
                    'min:1',
                    'max:' . self::MAX_IMAGES,
                ],

                'AnhMinhChung.*' => [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],

                'VideoMinhChung' => [
                    'nullable',
                    'file',
                    'mimetypes:video/mp4,video/quicktime,video/webm',
                    'max:20480',   // 20MB
                ],
                
            ],
            [
                'DonHangID.required' =>
                    'Vui lòng chọn đơn hàng.',

                'DonHangID.integer' =>
                    'Đơn hàng không hợp lệ.',

                'MoTa.required' =>
                    'Vui lòng nhập mô tả chi tiết.',

                'MoTa.max' =>
                    'Mô tả tối đa 1000 ký tự.',

                'AnhMinhChung.required' =>
                    'Vui lòng thêm ít nhất 1 ảnh minh chứng.',

                'AnhMinhChung.min' =>
                    'Vui lòng thêm ít nhất 1 ảnh minh chứng.',

                'AnhMinhChung.max' =>
                    'Chỉ được tải tối đa ' .
                    self::MAX_IMAGES .
                    ' ảnh.',

                'AnhMinhChung.*.image' =>
                    'Tệp tải lên phải là ảnh.',

                'AnhMinhChung.*.mimes' =>
                    'Ảnh phải có dạng JPG, PNG hoặc WebP.',

                'AnhMinhChung.*.max' =>
                    'Mỗi ảnh tối đa 5MB.',

                'AnhMinhChung.*.uploaded' =>
                    'Tải ảnh lên thất bại, ảnh có thể quá lớn.',
            ]
        );


        // ========================================
        // KIỂM TRA ĐƠN CÓ ĐỦ ĐIỀU KIỆN KHÔNG
        // ========================================

        $order = $this->eligibleOrders($userId)
            ->firstWhere(
                'DonHangID',
                (int) $data['DonHangID']
            );

        if (!$order) {

            return back()
                ->withInput()
                ->withErrors([
                    'DonHangID' =>
                        'Đơn hàng này không đủ điều kiện hoàn tiền ' .
                        '(đã có yêu cầu, chưa giao hoặc quá ' .
                        self::REFUND_DAYS .
                        ' ngày).',
                ]);
        }


        // ========================================
        // LƯU ẢNH
        // storage/app/public/refunds
        // ========================================

        $paths = [];

        foreach (
            $request->file('AnhMinhChung')
            as $file
        ) {
            $paths[] = $file->store(
                'refunds',
                'public'
            );
        }


        try {

            DB::transaction(function () use (
                $order,
                $userId,
                $data,
                $paths
            ) {

                // ========================================
                // KHÓA ĐƠN HÀNG
                // Tránh gửi yêu cầu trùng
                // ========================================

                DonHang::query()->from('DonHang')
                    ->where(
                        'DonHangID',
                        $order->DonHangID
                    )
                    ->lockForUpdate()
                    ->first();


                // ========================================
                // KIỂM TRA ĐÃ CÓ YÊU CẦU CHƯA
                // ========================================

                if (
                    YeuCauHoanTien::query()->from('YeuCauHoanTien')
                        ->where(
                            'DonHangID',
                            $order->DonHangID
                        )
                        ->exists()
                ) {
                    throw new \RuntimeException(
                        'duplicate'
                    );
                }


                // ========================================
                // TẠO YÊU CẦU HOÀN TIỀN
                // ========================================

                YeuCauHoanTien::query()->from('YeuCauHoanTien')->insert([

                    // Form hiện tại không nhập lý do, nên lưu giá trị mặc định
                    'LyDo' => 'Khác',

                    'MoTa' => $data['MoTa'],

                    'AnhMinhChung' => json_encode(
                        $paths,
                        JSON_UNESCAPED_UNICODE
                    ),

                    'VideoMinhChung' => null,

                    // Hoàn toàn bộ tiền của đơn
                    'SoTienHoan' => $order->TongTien,

                    // Trạng thái ban đầu
                    'TrangThai' => 'ChoXuLy',

                    'GhiChuXuLy' => null,

                    'NgayYeuCau' => now(),

                    'NgayXuLy' => null,

                    'DonHangID' => $order->DonHangID,

                    'NguoiDungID' => $userId,

                    'NguoiXuLyID' => null,
                ]);
            });


        } catch (\Throwable $e) {

            // ========================================
            // NẾU LƯU DATABASE THẤT BẠI
            // XÓA CÁC ẢNH ĐÃ UPLOAD
            // ========================================

            foreach ($paths as $path) {

                Storage::disk('public')
                    ->delete($path);

            }


            // ========================================
            // ĐÃ CÓ YÊU CẦU TRƯỚC ĐÓ
            // ========================================

            if ($e->getMessage() === 'duplicate') {

                return back()
                    ->withInput()
                    ->withErrors([
                        'DonHangID' =>
                            'Đơn hàng này đã có yêu cầu hoàn tiền.',
                    ]);
            }


            throw $e;
        }


        // ========================================
        // THÀNH CÔNG
        // ========================================

        return redirect()
            ->route('refund.index')
            ->with(
                'success',
                'Đã gửi yêu cầu hoàn tiền. Shop sẽ xử lý trong thời gian sớm nhất.'
            );
    }


    /**
 * Khách hủy yêu cầu hoàn tiền (chỉ khi còn chờ xử lý)
 */
public function cancel($id)
{
    if (!session()->has('NguoiDungID')) {
        return redirect()->route('login');
    }

    $userId = (int) session('NguoiDungID');

    // Chỉ yêu cầu của chính khách và còn đang chờ xử lý
    $refund = YeuCauHoanTien::query()->from('YeuCauHoanTien')
        ->where('YeuCauHoanTienID', (int) $id)
        ->where('NguoiDungID', $userId)
        ->whereIn('TrangThai', ['ChoXuLy', 'Chờ xử lý'])
        ->first();

    if (!$refund) {
        return redirect()->route('refund.index')
            ->with('error', 'Không thể hủy yêu cầu này (yêu cầu không tồn tại hoặc đã được shop xử lý).');
    }

    // Xóa có điều kiện: nếu shop vừa duyệt xong thì không xóa nhầm
    $deleted = YeuCauHoanTien::query()->from('YeuCauHoanTien')
        ->where('YeuCauHoanTienID', $refund->YeuCauHoanTienID)
        ->whereIn('TrangThai', ['ChoXuLy', 'Chờ xử lý'])
        ->delete();

    if ($deleted === 0) {
        return redirect()->route('refund.index')
            ->with('error', 'Yêu cầu vừa được shop xử lý nên không thể hủy.');
    }

    // Xóa ảnh và video đã tải lên
    foreach (json_decode($refund->AnhMinhChung ?? '', true) ?: [] as $path) {
        Storage::disk('public')->delete($path);
    }

    if (!empty($refund->VideoMinhChung)) {
        Storage::disk('public')->delete($refund->VideoMinhChung);
    }

    return redirect()->route('refund.index')
        ->with('success', 'Đã hủy yêu cầu hoàn tiền.');
}


    /**
     * Xem chi tiết một yêu cầu hoàn tiền
     */
    public function show($id)
    {
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $userId = (int) session('NguoiDungID');


        $refund = YeuCauHoanTien::query()->from('YeuCauHoanTien as yc')
            ->join(
                'DonHang as dh',
                'dh.DonHangID',
                '=',
                'yc.DonHangID'
            )
            ->where(
                'yc.YeuCauHoanTienID',
                (int) $id
            )
            ->where(
                'yc.NguoiDungID',
                $userId
            )
            ->select(
                'yc.*',
                'dh.MaDonHang',
                'dh.TongTien',
                'dh.TrangThai as TrangThaiDonHang'
            )
            ->first();


        if (!$refund) {
            abort(404);
        }


        $user = NguoiDung::query()->from('NguoiDung')
            ->where(
                'NguoiDungID',
                $userId
            )
            ->first();


        $danhMucs = DanhMuc::query()->get();


        return view(
            'user.account.refund-detail',
            compact(
                'refund',
                'user',
                'danhMucs'
            )
        );
    }


    /**
     * Lấy các đơn hàng đủ điều kiện yêu cầu hoàn tiền.
     *
     * Điều kiện:
     * - Thuộc người dùng hiện tại
     * - Đơn đã giao hoặc hoàn thành
     * - Chưa có yêu cầu hoàn tiền
     * - Chưa quá 7 ngày kể từ lúc giao
     */
    private function eligibleOrders(int $userId)
    {
        $orders = DonHang::query()->from('DonHang as dh')
            ->where(
                'dh.NguoiDungID',
                $userId
            )
            ->whereIn(
                'dh.TrangThai',
                [
                    'DaGiao',
                    'HoanThanh',
                ]
            )
            ->whereNotExists(function ($q) {

                $q->select(
                    DB::raw(1)
                )
                ->from(
                    'YeuCauHoanTien as yc'
                )
                ->whereColumn(
                    'yc.DonHangID',
                    'dh.DonHangID'
                );
            })
            ->select(
                'dh.DonHangID',
                'dh.MaDonHang',
                'dh.TongTien',
                'dh.NgayCapNhat'
            )
            ->orderByDesc(
                'dh.DonHangID'
            )
            ->get();


        if ($orders->isEmpty()) {
            return $orders;
        }


        $ids = $orders->pluck(
            'DonHangID'
        );


        // ========================================
        // LẤY THỜI ĐIỂM ĐƠN ĐƯỢC GIAO
        // ========================================

        $deliveredAt = collect();


        if (
            Schema::hasTable('LichSuDonHang')
        ) {

            $deliveredAt = LichSuDonHang::query()->from('LichSuDonHang')
                ->whereIn(
                    'DonHangID',
                    $ids
                )
                ->where(
                    'TrangThaiMoi',
                    'DaGiao'
                )
                ->groupBy(
                    'DonHangID'
                )
                ->selectRaw(
                    'DonHangID, MAX(NgayCapNhat) as GiaoLuc'
                )
                ->pluck(
                    'GiaoLuc',
                    'DonHangID'
                );
        }


        // ========================================
        // LẤY TÊN SẢN PHẨM TRONG ĐƠN
        // ========================================

        $names = ChiTietDonHang::query()->from('ChiTietDonHang')
            ->whereIn(
                'DonHangID',
                $ids
            )
            ->orderBy(
                'ChiTietDonHangID'
            )
            ->get([
                'DonHangID',
                'TenSanPham',
            ])
            ->groupBy(
                'DonHangID'
            );


        // ========================================
        // LỌC ĐƠN CÒN TRONG 7 NGÀY
        // ========================================

        return $orders

            ->filter(function ($o) use (
                $deliveredAt
            ) {

                $at =
                    $deliveredAt[
                        $o->DonHangID
                    ]
                    ?? $o->NgayCapNhat;


                return Carbon::parse($at)
                    ->addDays(
                        self::REFUND_DAYS
                    )
                    ->isFuture();
            })


            ->map(function ($o) use (
                $names
            ) {

                $tenSp = $names
                    ->get(
                        $o->DonHangID,
                        collect()
                    )
                    ->pluck(
                        'TenSanPham'
                    )
                    ->unique()
                    ->implode(', ');


  

                $o->Nhan = Str::limit(
                    $o->MaDonHang .
                    ' - ' .
                    $tenSp,
                    70
                );


                return $o;
            })


            ->values();
    }
}