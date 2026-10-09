<?php

namespace App\Http\Controllers\user;

use App\Models\BienThe;
use App\Models\DanhMuc;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietGioHang;
use App\Models\DiaChiNguoiDung;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\MaGiamGia;
use App\Models\NguoiDung;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = session('NguoiDungID');

        // Lấy thông tin người dùng
        $user = NguoiDung::query()->from('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if (!$user) {
            session()->forget('NguoiDungID');

            return redirect()->route('login');
        }

        // Lấy địa chỉ của người dùng
        $addresses = DiaChiNguoiDung::query()->from('DiaChiNguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->orderByDesc('MacDinh')
            ->orderByDesc('DiaChiNguoiDungID')
            ->get();

        // Danh mục cho navbar
        $danhMucs = DanhMuc::query()
            ->where('TrangThai', 'HoatDong')
            ->get();

        // Danh sách tỉnh/thành phố
        $provinces = [
            'Tỉnh Lai Châu',
            'Tỉnh Điện Biên',
            'Tỉnh Sơn La',
            'Tỉnh Lạng Sơn',
            'Tỉnh Cao Bằng',
            'Tỉnh Tuyên Quang',
            'Tỉnh Lào Cai',
            'Tỉnh Thái Nguyên',
            'Tỉnh Phú Thọ',
            'Tỉnh Bắc Ninh',
            'Tỉnh Hưng Yên',
            'Tỉnh Ninh Bình',
            'Tỉnh Thanh Hóa',
            'Tỉnh Nghệ An',
            'Tỉnh Hà Tĩnh',
            'Tỉnh Quảng Trị',
            'Tỉnh Quảng Ngãi',
            'Tỉnh Gia Lai',
            'Tỉnh Khánh Hòa',
            'Tỉnh Đắk Lắk',
            'Tỉnh Lâm Đồng',
            'Tỉnh Đồng Nai',
            'Tỉnh Tây Ninh',
            'Tỉnh Đồng Tháp',
            'Tỉnh Vĩnh Long',
            'Tỉnh An Giang',
            'Tỉnh Cà Mau',
            'Tỉnh Quảng Ninh',
            'Thành phố Hà Nội',
            'Thành phố Hải Phòng',
            'Thành phố Huế',
            'Thành phố Đà Nẵng',
            'Thành phố Hồ Chí Minh',
            'Thành phố Cần Thơ'
        ];


        // =========================================================
// LẤY SẢN PHẨM CHO CHECKOUT
// =========================================================

$items = collect();

$bienTheID = $request->input('BienTheID');
$soLuongMuaNgay = (int) $request->input('SoLuong', 1);


// =========================================================
// TRƯỜNG HỢP MUA NGAY
// =========================================================

if ($bienTheID) {

    $bienThe = BienThe::query()->from('BienThe')
        ->join(
            'SanPham',
            'BienThe.SanPhamID',
            '=',
            'SanPham.SanPhamID'
        )
        ->leftJoin(
            'HinhAnhSanPham',
            function ($join) {
                $join->on(
                    'SanPham.SanPhamID',
                    '=',
                    'HinhAnhSanPham.SanPhamID'
                )
                ->where(
                    'HinhAnhSanPham.AnhDaiDien',
                    1
                );
            }
        )
        ->where(
            'BienThe.BienTheID',
            $bienTheID
        )
        ->where(
            'SanPham.TrangThai',
            'HoatDong'
        )
        ->select(
            'BienThe.BienTheID',
            'BienThe.SanPhamID',
            'BienThe.MauSac',
            'BienThe.KichThuoc',
            'BienThe.GiaBienThe',
            'BienThe.SoLuong as TonKho',
            'SanPham.TenSanPham',
            'HinhAnhSanPham.DuongDanAnh'
        )
        ->first();


    if ($bienThe) {

        $items->push((object) [
            'BienTheID' => $bienThe->BienTheID,
            'SanPhamID' => $bienThe->SanPhamID,
            'TenSanPham' => $bienThe->TenSanPham,
            'MauSac' => $bienThe->MauSac,
            'KichThuoc' => $bienThe->KichThuoc,
            'GiaBienThe' => $bienThe->GiaBienThe,
            'SoLuong' => $soLuongMuaNgay,
            'TonKho' => $bienThe->TonKho,
            'HinhAnh' => $bienThe->DuongDanAnh,
        ]);
    }
}


// =========================================================
// TRƯỜNG HỢP ĐI TỪ GIỎ HÀNG
// =========================================================

else {

    $gioHang = GioHang::query()->from('GioHang')
        ->where(
            'NguoiDungID',
            $nguoiDungID
        )
        ->first();


    if ($gioHang) {

        $items = ChiTietGioHang::query()->from('ChiTietGioHang')
            ->join(
                'BienThe',
                'ChiTietGioHang.BienTheID',
                '=',
                'BienThe.BienTheID'
            )
            ->join(
                'SanPham',
                'BienThe.SanPhamID',
                '=',
                'SanPham.SanPhamID'
            )
            ->leftJoin(
                'HinhAnhSanPham',
                function ($join) {
                    $join->on(
                        'SanPham.SanPhamID',
                        '=',
                        'HinhAnhSanPham.SanPhamID'
                    )
                    ->where(
                        'HinhAnhSanPham.AnhDaiDien',
                        1
                    );
                }
            )
            ->where(
                'ChiTietGioHang.GioHangID',
                $gioHang->GioHangID
            )
            ->where(
                'SanPham.TrangThai',
                'HoatDong'
            )
            ->select(
                'ChiTietGioHang.BienTheID',
                'BienThe.SanPhamID',
                'SanPham.TenSanPham',
                'BienThe.MauSac',
                'BienThe.KichThuoc',
                'BienThe.GiaBienThe',
                'BienThe.SoLuong as TonKho',
                'ChiTietGioHang.SoLuong',
                'HinhAnhSanPham.DuongDanAnh as HinhAnh'
            )
            ->get();
    }
}


// =========================================================
// TÍNH TẠM TÍNH
// =========================================================

$subtotal = $items->sum(function ($item) {
    return $item->GiaBienThe * $item->SoLuong;
});


        $defaultAddress = $addresses->firstWhere('MacDinh', 1)
            ?? $addresses->first();

        $normalizedCity = Str::lower(
            Str::ascii(trim($defaultAddress->ThanhPho ?? ''))
        );

        $shippingFee = $normalizedCity === 'thanh pho ho chi minh'
            ? 0
            : 35000;

        $total = $subtotal + $shippingFee;
        return view('user.products.checkout', compact(
            'user',
            'addresses',
            'danhMucs',
            'provinces',
            'items',
            'subtotal',
            'shippingFee',
            'total'
        ));
    }



  public function placeOrder(Request $request)
{
    // =========================================================
    // KIỂM TRA ĐĂNG NHẬP
    // =========================================================

    if (!session()->has('NguoiDungID')) {
        return redirect()->route('login');
    }

    $nguoiDungID = (int) session('NguoiDungID');


    // =========================================================
    // VALIDATE
    // =========================================================

    $request->validate(
        [
            'DiaChiNguoiDungID' => 'required|exists:DiaChiNguoiDung,DiaChiNguoiDungID',
            'PhuongThucThanhToan' => 'required|in:cod,bank_transfer',
        ],
        [
            'DiaChiNguoiDungID.required' => 'Vui lòng chọn địa chỉ giao hàng.',
            'DiaChiNguoiDungID.exists' => 'Địa chỉ giao hàng không hợp lệ.',
            'PhuongThucThanhToan.required' => 'Vui lòng chọn phương thức thanh toán.',
            'PhuongThucThanhToan.in' => 'Phương thức thanh toán không hợp lệ.',
        ]
    );


    // =========================================================
    // KIỂM TRA ĐỊA CHỈ CÓ THUỘC VỀ NGƯỜI DÙNG KHÔNG
    // =========================================================

    $diaChi = DiaChiNguoiDung::query()->from('DiaChiNguoiDung')
        ->where('DiaChiNguoiDungID', $request->DiaChiNguoiDungID)
        ->where('NguoiDungID', $nguoiDungID)
        ->first();

    if (!$diaChi) {
        return back()
            ->withInput()
            ->withErrors([
                'DiaChiNguoiDungID' => 'Địa chỉ không hợp lệ.'
            ]);
    }


    // =========================================================
    // LẤY SẢN PHẨM
    // =========================================================

    $items = collect();

    // Trường hợp Mua ngay
    $bienTheID = $request->input('BienTheID');

    // Số lượng mua ngay
    $soLuongMuaNgay = max(
        1,
        (int) $request->input('SoLuong', 1)
    );

    // Giỏ hàng
    $gioHang = null;


    // =========================================================
    // TRƯỜNG HỢP MUA NGAY
    // =========================================================

    if ($bienTheID) {

        $bienThe = BienThe::query()->from('BienThe')
            ->join(
                'SanPham',
                'BienThe.SanPhamID',
                '=',
                'SanPham.SanPhamID'
            )
            ->leftJoin(
                'HinhAnhSanPham',
                function ($join) {
                    $join->on(
                        'SanPham.SanPhamID',
                        '=',
                        'HinhAnhSanPham.SanPhamID'
                    )
                    ->where(
                        'HinhAnhSanPham.AnhDaiDien',
                        1
                    );
                }
            )
            ->where(
                'BienThe.BienTheID',
                $bienTheID
            )
            ->where(
                'SanPham.TrangThai',
                'HoatDong'
            )
            ->select(
                'BienThe.BienTheID',
                'BienThe.SanPhamID',
                'BienThe.MauSac',
                'BienThe.KichThuoc',
                'BienThe.GiaBienThe',
                'BienThe.SoLuong as TonKho',
                'BienThe.SoLuongTamGiu',
                'SanPham.TenSanPham',
                'HinhAnhSanPham.DuongDanAnh'
            )
            ->first();


        if ($bienThe) {

            $items->push(
                (object) [
                    'BienTheID' => $bienThe->BienTheID,
                    'SanPhamID' => $bienThe->SanPhamID,
                    'TenSanPham' => $bienThe->TenSanPham,
                    'MauSac' => $bienThe->MauSac,
                    'KichThuoc' => $bienThe->KichThuoc,
                    'GiaBienThe' => $bienThe->GiaBienThe,
                    'SoLuong' => $soLuongMuaNgay,
                    'TonKho' => $bienThe->TonKho,
                    'SoLuongTamGiu' => $bienThe->SoLuongTamGiu,
                    'HinhAnh' => $bienThe->DuongDanAnh,
                ]
            );
        }


    // =========================================================
    // TRƯỜNG HỢP ĐẶT TỪ GIỎ HÀNG
    // =========================================================

    } else {

        $gioHang = GioHang::query()->from('GioHang')
            ->where(
                'NguoiDungID',
                $nguoiDungID
            )
            ->first();


        if ($gioHang) {

            $items = ChiTietGioHang::query()->from('ChiTietGioHang')
                ->join(
                    'BienThe',
                    'ChiTietGioHang.BienTheID',
                    '=',
                    'BienThe.BienTheID'
                )
                ->join(
                    'SanPham',
                    'BienThe.SanPhamID',
                    '=',
                    'SanPham.SanPhamID'
                )
                ->leftJoin(
                    'HinhAnhSanPham',
                    function ($join) {

                        $join->on(
                            'SanPham.SanPhamID',
                            '=',
                            'HinhAnhSanPham.SanPhamID'
                        )
                        ->where(
                            'HinhAnhSanPham.AnhDaiDien',
                            1
                        );
                    }
                )
                ->where(
                    'ChiTietGioHang.GioHangID',
                    $gioHang->GioHangID
                )
                ->where(
                    'SanPham.TrangThai',
                    'HoatDong'
                )
                ->select(
                    'ChiTietGioHang.BienTheID',
                    'BienThe.SanPhamID',
                    'SanPham.TenSanPham',
                    'BienThe.MauSac',
                    'BienThe.KichThuoc',
                    'BienThe.GiaBienThe',
                    'BienThe.SoLuong as TonKho',
                    'BienThe.SoLuongTamGiu',
                    'ChiTietGioHang.SoLuong',
                    'HinhAnhSanPham.DuongDanAnh as HinhAnh'
                )
                ->get();
        }
    }


    // =========================================================
    // KIỂM TRA GIỎ HÀNG
    // =========================================================

    if ($items->isEmpty()) {

        return back()
            ->withInput()
            ->withErrors([
                'items' => 'Giỏ hàng trống, không thể đặt hàng.'
            ]);
    }


    // =========================================================
    // TÍNH TẠM TÍNH
    // =========================================================

    $subtotal = $items->sum(
        function ($item) {
            return $item->GiaBienThe * $item->SoLuong;
        }
    );


    // =========================================================
    // TÍNH PHÍ VẬN CHUYỂN
    // =========================================================

    $normalizedCity = Str::lower(
        Str::ascii(
            trim($diaChi->ThanhPho ?? '')
        )
    );


    $shippingFee =
        $normalizedCity === 'thanh pho ho chi minh'
            ? 0
            : 35000;


    // =========================================================
    // KIỂM TRA MÃ GIẢM GIÁ
    // =========================================================

    $soTienGiam = 0;
    $maGiamGiaID = null;


    if ($request->filled('MaCode')) {

        $maGiamGia = MaGiamGia::query()->from('magiamgia')
            ->where(
                'MaCode',
                trim($request->MaCode)
            )
            ->where(
                'TrangThai',
                'HoatDong'
            )
            ->where(
                'NgayHetHan',
                '>=',
                now()
            )
            ->first();


        if (
            $maGiamGia &&
            $subtotal >= $maGiamGia->GiaTriDonHangToiThieu
        ) {

            $soTienGiam = round(
                $subtotal *
                $maGiamGia->GiaTriGiam /
                100
            );

            $maGiamGiaID =
                $maGiamGia->MaGiamGiaID;
        }
    }


    // =========================================================
    // TỔNG TIỀN
    // =========================================================

    $total =
        $subtotal +
        $shippingFee -
        $soTienGiam;


    // =========================================================
    // BẮT ĐẦU TRANSACTION
    // =========================================================

    DB::beginTransaction();


    try {

        // =====================================================
        // KIỂM TRA TỒN KHO + KHÓA BIẾN THỂ
        //
        // SoLuong:
        //     Tồn kho thực tế
        //
        // SoLuongTamGiu:
        //     Số lượng đang được các đơn chưa xác nhận giữ
        //
        // Số lượng có thể mua:
        //
        // SoLuong - SoLuongTamGiu
        // =====================================================

        foreach ($items as $item) {

            $bienThe = BienThe::query()->from('BienThe')
                ->where(
                    'BienTheID',
                    $item->BienTheID
                )
                ->lockForUpdate()
                ->first();


            if (!$bienThe) {

                throw new \Exception(
                    'Không tìm thấy sản phẩm "' .
                    $item->TenSanPham .
                    '".'
                );
            }


            // Số lượng thực tế có thể bán
            $soLuongCoTheBan =
                (int) $bienThe->SoLuong -
                (int) $bienThe->SoLuongTamGiu;


            // =================================================
            // KHÔNG ĐỦ HÀNG
            // =================================================

            if ($soLuongCoTheBan < $item->SoLuong) {

                throw new \Exception(
                    'Sản phẩm "' .
                    $item->TenSanPham .
                    '" không đủ số lượng để đặt. ' .
                    'Số lượng có thể mua hiện tại: ' .
                    $soLuongCoTheBan .
                    ', số lượng cần mua: ' .
                    $item->SoLuong .
                    '.'
                );
            }


            // =================================================
            // CHỈ TĂNG SỐ LƯỢNG TẠM GIỮ
            //
            // KHÔNG TRỪ SoLuong
            // =================================================

            BienThe::query()->from('BienThe')
                ->where(
                    'BienTheID',
                    $item->BienTheID
                )
                ->increment(
                    'SoLuongTamGiu',
                    $item->SoLuong
                );
        }


        // =====================================================
        // TẠO ĐƠN HÀNG
        // =====================================================

        $donHangID = DonHang::query()->from('DonHang')
            ->insertGetId(
                [
                    'NguoiDungID' =>
                        $nguoiDungID,

                    'MaDonHang' =>
                        '',

                    'TongTien' =>
                        $total,

                    'TenNguoiNhan' =>
                        $diaChi->TenNguoiNhan,

                    'SoDienThoaiNguoiNhan' =>
                        $diaChi->SoDienThoai,

                    'DiaChiNhanHang' =>
                        $diaChi->DiaChi .
                        ', ' .
                        $diaChi->ThanhPho,

                    // Đơn mới → chờ xác nhận
                    'TrangThai' =>
                        'Chờ xác nhận',

                    'PhuongThucThanhToan' =>
                        $request->PhuongThucThanhToan,

                    'MaGiamGiaID' =>
                        $maGiamGiaID,

                    'SoTienGiam' =>
                        $soTienGiam,

                    'TrangThaiThanhToan' =>
                        'Chưa thanh toán',

                    'NgayTao' =>
                        now(),

                    'NgayCapNhat' =>
                        now(),
                ]
            );


        // =====================================================
        // TẠO MÃ ĐƠN HÀNG
        // =====================================================

        $maDonHang =
            'DH' .
            str_pad(
                $donHangID,
                3,
                '0',
                STR_PAD_LEFT
            );


        DonHang::query()->from('DonHang')
            ->where(
                'DonHangID',
                $donHangID
            )
            ->update(
                [
                    'MaDonHang' =>
                        $maDonHang
                ]
            );


        // =====================================================
        // TẠO CHI TIẾT ĐƠN HÀNG
        //
        // LƯU Ý:
        // Không trừ SoLuong ở đây.
        // Vì SoLuongTamGiu đã được tăng ở phía trên.
        // =====================================================

        foreach ($items as $item) {

            ChiTietDonHang::query()->from('ChiTietDonHang')
                ->insert(
                    [
                        'DonHangID' =>
                            $donHangID,

                        'SanPhamID' =>
                            $item->SanPhamID,

                        'TenSanPham' =>
                            $item->TenSanPham,

                        'MauSac' =>
                            $item->MauSac,

                        'KichThuoc' =>
                            $item->KichThuoc,

                        'HinhAnh' =>
                            $item->HinhAnh,

                        'SoLuong' =>
                            $item->SoLuong,

                        'GiaTaiThoiDiemMua' =>
                            $item->GiaBienThe,
                    ]
                );
        }


        // =====================================================
        // XÓA SẢN PHẨM KHỎI GIỎ HÀNG
        // =====================================================

        if ($gioHang) {

            ChiTietGioHang::query()->from('ChiTietGioHang')
                ->where(
                    'GioHangID',
                    $gioHang->GioHangID
                )
                ->delete();
        }


        // =====================================================
        // HOÀN TẤT TRANSACTION
        // =====================================================

        DB::commit();


    } catch (\Exception $e) {

        // Nếu có lỗi:
        // hoàn tác cả việc tăng SoLuongTamGiu
        // và tạo đơn hàng.

        DB::rollBack();


        return back()
            ->withInput()
            ->withErrors(
                [
                    'order' =>
                        'Đặt hàng thất bại: ' .
                        $e->getMessage()
                ]
            );
    }


    // =========================================================
    // CHUYỂN SANG TRANG ĐẶT HÀNG THÀNH CÔNG
    // =========================================================

    return redirect()
        ->route(
            'checkout.success',
            [
                'maDonHang' =>
                    $maDonHang
            ]
        );
}

public function success($maDonHang)
{
    if (!session()->has('NguoiDungID')) {
        return redirect()->route('login');
    }

    $nguoiDungID = session('NguoiDungID');

    $donHang = DonHang::query()->from('DonHang')
        ->where('MaDonHang', $maDonHang)
        ->where('NguoiDungID', $nguoiDungID)
        ->first();

    if (!$donHang) {
        abort(404);
    }

    $chiTiet = ChiTietDonHang::query()->from('ChiTietDonHang')
        ->where('DonHangID', $donHang->DonHangID)
        ->get();

    $danhMucs = DanhMuc::query()
        ->where('TrangThai', 'HoatDong')
        ->get();

    return view('user.products.checkout-success', compact('donHang', 'chiTiet', 'danhMucs'));
}


public function applyCoupon(Request $request)
{
    if (!session()->has('NguoiDungID')) {
        return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
    }

    $request->validate([
        'MaCode' => 'required|string',
        'Subtotal' => 'required|numeric',
    ]);

    $subtotal = (float) $request->Subtotal;

    $maGiamGia = MaGiamGia::query()->from('magiamgia')
        ->where('MaCode', trim($request->MaCode))
        ->where('TrangThai', 'HoatDong')
        ->where('NgayHetHan', '>=', now())
        ->first();

    if (!$maGiamGia) {
        return response()->json([
            'success' => false,
            'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.',
        ]);
    }

    if ($subtotal < $maGiamGia->GiaTriDonHangToiThieu) {
        return response()->json([
            'success' => false,
            'message' => 'Đơn hàng cần tối thiểu ' .
                number_format($maGiamGia->GiaTriDonHangToiThieu, 0, ',', '.') .
                ' VND để dùng mã này.',
        ]);
    }

    // Tính số tiền giảm (GiaTriGiam là %)
    $soTienGiam = round($subtotal * $maGiamGia->GiaTriGiam / 100);

    return response()->json([
        'success' => true,
        'message' => 'Áp dụng mã giảm giá thành công!',
        'MaGiamGiaID' => $maGiamGia->MaGiamGiaID,
        'MaCode' => $maGiamGia->MaCode,
        'GiaTriGiam' => $maGiamGia->GiaTriGiam,
        'SoTienGiam' => $soTienGiam,
    ]);
}




}