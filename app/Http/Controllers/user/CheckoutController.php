<?php

namespace App\Http\Controllers\user;
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
        $user = DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if (!$user) {
            session()->forget('NguoiDungID');

            return redirect()->route('login');
        }

        // Lấy địa chỉ của người dùng
        $addresses = DB::table('DiaChiNguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->orderByDesc('MacDinh')
            ->orderByDesc('DiaChiNguoiDungID')
            ->get();

        // Danh mục cho navbar
        $danhMucs = DB::table('DanhMuc')
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

    $bienThe = DB::table('BienThe')
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

    $gioHang = DB::table('GioHang')
        ->where(
            'NguoiDungID',
            $nguoiDungID
        )
        ->first();


    if ($gioHang) {

        $items = DB::table('ChiTietGioHang')
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
    if (!session()->has('NguoiDungID')) {
        return redirect()->route('login');
    }

    $nguoiDungID = session('NguoiDungID');

    // =========================================================
    // VALIDATE
    // =========================================================

    $request->validate([
        'DiaChiNguoiDungID' => 'required|exists:DiaChiNguoiDung,DiaChiNguoiDungID',
        'PhuongThucThanhToan' => 'required|in:cod,bank_transfer',
    ], [
        'DiaChiNguoiDungID.required' => 'Vui lòng chọn địa chỉ giao hàng.',
        'PhuongThucThanhToan.required' => 'Vui lòng chọn phương thức thanh toán.',
    ]);

    $diaChi = DB::table('DiaChiNguoiDung')
        ->where('DiaChiNguoiDungID', $request->DiaChiNguoiDungID)
        ->where('NguoiDungID', $nguoiDungID)
        ->first();

    if (!$diaChi) {
        return back()->withErrors(['DiaChiNguoiDungID' => 'Địa chỉ không hợp lệ.']);
    }

    // =========================================================
    // LẤY LẠI DANH SÁCH SẢN PHẨM (giống logic index)
    // =========================================================

    $items = collect();
    $bienTheID = $request->input('BienTheID');
    $soLuongMuaNgay = (int) $request->input('SoLuong', 1);
    $gioHang = null;

    if ($bienTheID) {

        $bienThe = DB::table('BienThe')
            ->join('SanPham', 'BienThe.SanPhamID', '=', 'SanPham.SanPhamID')
            ->leftJoin('HinhAnhSanPham', function ($join) {
                $join->on('SanPham.SanPhamID', '=', 'HinhAnhSanPham.SanPhamID')
                     ->where('HinhAnhSanPham.AnhDaiDien', 1);
            })
            ->where('BienThe.BienTheID', $bienTheID)
            ->where('SanPham.TrangThai', 'HoatDong')
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

    } else {

        $gioHang = DB::table('GioHang')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if ($gioHang) {
            $items = DB::table('ChiTietGioHang')
                ->join('BienThe', 'ChiTietGioHang.BienTheID', '=', 'BienThe.BienTheID')
                ->join('SanPham', 'BienThe.SanPhamID', '=', 'SanPham.SanPhamID')
                ->leftJoin('HinhAnhSanPham', function ($join) {
                    $join->on('SanPham.SanPhamID', '=', 'HinhAnhSanPham.SanPhamID')
                         ->where('HinhAnhSanPham.AnhDaiDien', 1);
                })
                ->where('ChiTietGioHang.GioHangID', $gioHang->GioHangID)
                ->where('SanPham.TrangThai', 'HoatDong')
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

    if ($items->isEmpty()) {
        return back()->withErrors(['items' => 'Giỏ hàng trống, không thể đặt hàng.']);
    }

    // =========================================================
    // TÍNH TIỀN
    // =========================================================

    $subtotal = $items->sum(fn($item) => $item->GiaBienThe * $item->SoLuong);

    $normalizedCity = Str::lower(Str::ascii(trim($diaChi->ThanhPho ?? '')));
    $shippingFee = $normalizedCity === 'thanh pho ho chi minh' ? 0 : 35000;
    $total = $subtotal + $shippingFee;

    // =========================================================
    // LƯU DB
    // =========================================================

DB::beginTransaction();

try {

    // Lấy đơn hàng có ID lớn nhất để tính số thứ tự tiếp theo
    $lastOrder = DB::table('DonHang')
        ->lockForUpdate()
        ->orderByDesc('DonHangID')
        ->first();

    $nextNumber = $lastOrder ? ($lastOrder->DonHangID + 1) : 1;
    $maDonHang = 'DH' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    $donHangID = DB::table('DonHang')->insertGetId([
        'NguoiDungID' => $nguoiDungID,
        'MaDonHang' => $maDonHang,
        'TongTien' => $total,
        'TenNguoiNhan' => $diaChi->TenNguoiNhan,
        'SoDienThoaiNguoiNhan' => $diaChi->SoDienThoai,
        'DiaChiNhanHang' => $diaChi->DiaChi . ', ' . $diaChi->ThanhPho,
        'TrangThai' => 'ChoXacNhan',
        'PhuongThucThanhToan' => $request->PhuongThucThanhToan,
        'TrangThaiThanhToan' => 'ChuaThanhToan',
        'NgayTao' => now(),
        'NgayCapNhat' => now(),
    ]);


        foreach ($items as $item) {
            DB::table('ChiTietDonHang')->insert([
                'DonHangID' => $donHangID,
                'SanPhamID' => $item->SanPhamID,
                'TenSanPham' => $item->TenSanPham,
                'MauSac' => $item->MauSac,
                'KichThuoc' => $item->KichThuoc,
                'HinhAnh' => $item->HinhAnh,
                'SoLuong' => $item->SoLuong,
                'GiaTaiThoiDiemMua' => $item->GiaBienThe,
            ]);

            // Trừ tồn kho
            DB::table('BienThe')
                ->where('BienTheID', $item->BienTheID)
                ->decrement('SoLuong', $item->SoLuong);
        }

        // Xoá giỏ hàng nếu đặt từ giỏ hàng
        if ($gioHang) {
            DB::table('ChiTietGioHang')
                ->where('GioHangID', $gioHang->GioHangID)
                ->delete();
        }

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['order' => 'Đặt hàng thất bại: ' . $e->getMessage()]);
    }

    return redirect()
    ->route('checkout.success', ['maDonHang' => $maDonHang]);
}


public function success($maDonHang)
{
    if (!session()->has('NguoiDungID')) {
        return redirect()->route('login');
    }

    $nguoiDungID = session('NguoiDungID');

    $donHang = DB::table('DonHang')
        ->where('MaDonHang', $maDonHang)
        ->where('NguoiDungID', $nguoiDungID)
        ->first();

    if (!$donHang) {
        abort(404);
    }

    $chiTiet = DB::table('ChiTietDonHang')
        ->where('DonHangID', $donHang->DonHangID)
        ->get();

    $danhMucs = DB::table('DanhMuc')
        ->where('TrangThai', 'HoatDong')
        ->get();

    // Map trạng thái sang tiếng Việt có dấu để hiển thị
    $trangThaiLabels = [
        'ChoXacNhan' => 'Chờ xác nhận',
        'DaXacNhan' => 'Đã xác nhận',
        'DangGiao' => 'Đang giao',
        'DaGiao' => 'Đã giao',
        'DaHuy' => 'Đã hủy',
    ];

    $trangThaiThanhToanLabels = [
        'ChuaThanhToan' => 'Chưa thanh toán',
        'DaThanhToan' => 'Đã thanh toán',
    ];

    $phuongThucLabels = [
        'cod' => 'Thanh toán khi nhận hàng',
        'bank_transfer' => 'Chuyển khoản ngân hàng',
    ];

    return view('user.products.checkout-success', compact(
        'donHang',
        'chiTiet',
        'danhMucs',
        'trangThaiLabels',
        'trangThaiThanhToanLabels',
        'phuongThucLabels'
    ));
}


}