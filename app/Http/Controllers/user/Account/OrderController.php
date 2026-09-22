<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // =========================================================
    // CÁC TAB TRÊN GIAO DIỆN
    // =========================================================

        private const TABS = [

        // Đơn mới
            'ChoXacNhan' => [
                'MoiTao',
                'ChoXacNhan',
                'Chờ xác nhận',
            ],

         // Đã xác nhận
            'DaXacNhan' => [
            'DaXacNhan',
            'Đã xác nhận',
         ],

            // Đang giao
            'DangGiao' => [
                'DangGiao',
               'DangGiaoHang',
               'Đang giao hàng',
            ],

            // Đã nhận
            'DaGiao' => [
            'HoanThanh',
            ],

            // Đã hủy
            'DaHuy' => [
             'DaHuy',
             'Đã hủy',
            ],
        ];

    // =========================================================
    // DANH SÁCH ĐƠN HÀNG CỦA TÔI
    // =========================================================

    public function index()
    {
        // Chưa đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = (int) session('NguoiDungID');

        $status = request('status');


        // =====================================================
        // THÔNG TIN NGƯỜI DÙNG
        // =====================================================

        $user = DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();


        // =====================================================
        // DANH MỤC CHO NAVBAR
        // =====================================================

        $danhMucs = DB::table('DanhMuc')
            ->get();


        // =====================================================
        // LẤY ĐƠN HÀNG
        // =====================================================

        $orderStatusCounts = DB::table('DonHang')
            ->where('NguoiDungID', $nguoiDungID)
            ->select(
                'TrangThai',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('TrangThai')
            ->pluck('total', 'TrangThai');

        $orderCounts = [
            'TatCa' => $orderStatusCounts->sum(),
            'ChoXacNhan' => $this->countStatuses(
                $orderStatusCounts,
                self::TABS['ChoXacNhan']
            ),
            'DaXacNhan' => $this->countStatuses(
                $orderStatusCounts,
                self::TABS['DaXacNhan']
            ),
            'DangGiao' => $this->countStatuses(
                $orderStatusCounts,
                self::TABS['DangGiao']
            ),
            'DaGiao' => $this->countStatuses(
                $orderStatusCounts,
                self::TABS['DaGiao']
            ),
            'DaHuy' => $this->countStatuses(
                $orderStatusCounts,
                self::TABS['DaHuy']
            ),
        ];

        $orders = DB::table('DonHang')
            ->where('NguoiDungID', $nguoiDungID)

            ->when(
                isset(self::TABS[$status]),
                function ($query) use ($status) {
                    $query->whereIn(
                        'TrangThai',
                        self::TABS[$status]
                    );
                }
            )

            ->orderByDesc('DonHangID')
            ->get();


        // =====================================================
        // LẤY SẢN PHẨM TRONG ĐƠN
        // =====================================================

        $items = DB::table('ChiTietDonHang')
            ->whereIn(
                'DonHangID',
                $orders->pluck('DonHangID')
            )

            ->select(
                'DonHangID',
                'SanPhamID',
                'TenSanPham',
                'MauSac',
                'KichThuoc',
                'HinhAnh',
                'SoLuong',
                'GiaTaiThoiDiemMua'
            )

            ->get()

            ->groupBy('DonHangID');

        $reviewedItems = DB::table('DanhGia')
            ->where('NguoiDungID', $nguoiDungID)
            ->whereIn('DonHangID', $orders->pluck('DonHangID'))
            ->get(['DonHangID', 'SanPhamID'])
            ->mapWithKeys(function ($review) {
                return [
                    $review->DonHangID . ':' . $review->SanPhamID => true,
                ];
            });


        // =====================================================
        // GẮN SẢN PHẨM VÀO TỪNG ĐƠN
        // =====================================================

        $orders->transform(function ($order) use ($items, $reviewedItems) {

            $order->items = $items->get(
                $order->DonHangID,
                collect()
            )->map(function ($item) use ($order, $reviewedItems) {
                $item->daDanhGia = $reviewedItems->has(
                    $order->DonHangID . ':' . $item->SanPhamID
                );

                return $item;
            });

            return $order;
        });


        // =====================================================
        // TRẢ VỀ VIEW
        // =====================================================

        return view(
            'user.account.orders',
            compact(
                'user',
                'danhMucs',
                'orders',
                'orderCounts'
            )
        );
    }

    private function countStatuses($counts, array $statuses)
    {
        return collect($statuses)
            ->sum(function ($status) use ($counts) {
                return (int) ($counts[$status] ?? 0);
            });
    }


    // =========================================================
    // KHÁCH HỦY ĐƠN
    // Chỉ được hủy khi đơn vẫn còn là MoiTao
    // =========================================================

    public function cancel(
    OrderStatusService $service,
    $id
) {
    // Chưa đăng nhập
    if (!session()->has('NguoiDungID')) {
        return redirect()->route('login');
    }

    $nguoiDungID = (int) session('NguoiDungID');

    DB::beginTransaction();

    try {

        // =====================================================
        // LẤY ĐƠN HÀNG CỦA KHÁCH VÀ KHÓA ĐƠN
        // =====================================================

        $order = DB::table('DonHang')
            ->where('DonHangID', (int) $id)
            ->where('NguoiDungID', $nguoiDungID)
            ->lockForUpdate()
            ->first();

        if (!$order) {
            DB::rollBack();

            return redirect()
                ->route('order.index')
                ->with(
                    'error',
                    'Không tìm thấy đơn hàng.'
                );
        }

        // Chỉ cho hủy khi đơn chưa được xác nhận
        if (!in_array($order->TrangThai, [
            'MoiTao',
            'ChoXacNhan',
            'Chờ xác nhận',
        ])) {

            DB::rollBack();

            return redirect()
                ->route('order.index')
                ->with(
                    'error',
                    'Không thể hủy đơn hàng này vì đơn đã được xác nhận.'
                );
        }


        // =====================================================
        // LẤY CHI TIẾT ĐƠN HÀNG
        // =====================================================

        $items = DB::table('ChiTietDonHang')
            ->where('DonHangID', $order->DonHangID)
            ->get();


        // =====================================================
        // CHUYỂN TRẠNG THÁI ĐƠN
        // =====================================================

        $ok = $service->change(
            (int) $id,
            'DaHuy',
            'khach_hang',
            'Khách hủy đơn',
            $nguoiDungID
        );


        if (!$ok) {

            DB::rollBack();

            return redirect()
                ->route('order.index')
                ->with(
                    'error',
                    'Không thể hủy đơn hàng này vì đơn đã được xác nhận hoặc không tồn tại.'
                );
        }


        // =====================================================
        // TRẢ LẠI SỐ LƯỢNG TẠM GIỮ
        //
        // SoLuong KHÔNG ĐỔI
        // SoLuongTamGiu GIẢM
        // =====================================================

        foreach ($items as $item) {

            DB::table('BienThe')
                ->where('SanPhamID', $item->SanPhamID)
                ->where('MauSac', $item->MauSac)
                ->where('KichThuoc', $item->KichThuoc)
                ->decrement(
                    'SoLuongTamGiu',
                    $item->SoLuong
                );
        }


        DB::commit();


        return redirect()
            ->route('order.index')
            ->with(
                'success',
                'Đã hủy đơn hàng.'
            );

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()
            ->route('order.index')
            ->with(
                'error',
                'Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage()
            );
    }
}


    // =========================================================
    // KHÁCH XÁC NHẬN ĐÃ NHẬN HÀNG
    //
    // DaGiao -> HoanThanh
    // =========================================================

    public function receive(
        OrderStatusService $service,
        $id
    ) {
        // Chưa đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = (int) session('NguoiDungID');


        // =====================================================
        // CHUYỂN:
        // DaGiao -> HoanThanh
        // =====================================================

        $ok = $service->change(
            (int) $id,
            'HoanThanh',
            'khach_hang',
            'Khách xác nhận đã nhận hàng',
            $nguoiDungID
        );


        // =====================================================
        // THÔNG BÁO
        // =====================================================

        return redirect()
            ->route('order.index')
            ->with(
                $ok ? 'success' : 'error',
                $ok
                    ? 'Cảm ơn bạn! Đơn hàng đã hoàn tất, bạn có thể đánh giá sản phẩm.'
                    : 'Không thể xác nhận đơn hàng này.'
            );
    }


    



}