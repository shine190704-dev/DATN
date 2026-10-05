<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOverviewController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA ĐĂNG NHẬP
        |--------------------------------------------------------------------------
        */

        if (! $request->session()->has('AdminNguoiDungID')) {
            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => 'Vui lòng đăng nhập để truy cập khu vực quản trị.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA QUYỀN
        |--------------------------------------------------------------------------
        */

        $vaiTro = $request->session()->get('AdminVaiTro');

        if (! in_array($vaiTro, ['Admin', 'NhanVien'])) {

            $request->session()->forget([
                'AdminNguoiDungID',
                'AdminHo',
                'AdminTen',
                'AdminEmail',
                'AdminVaiTro',
            ]);

            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => 'Tài khoản không có quyền truy cập khu vực quản trị.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | NGÀY THỐNG KÊ
        |--------------------------------------------------------------------------
        */

        $tuNgay = $request->input('tu_ngay') ?: now()->subDays(29)->toDateString();
        $denNgay = $request->input('den_ngay') ?: now()->toDateString();

        $request->merge([
            'tu_ngay' => $tuNgay,
            'den_ngay' => $denNgay,
        ]);

        $validated = $request->validate([
            'tu_ngay' => ['required', 'date', 'before_or_equal:den_ngay'],
            'den_ngay' => ['required', 'date', 'after_or_equal:tu_ngay'],
        ]);

        $tuNgay = $validated['tu_ngay'];
        $denNgay = $validated['den_ngay'];

        $ordersInPeriod = static function () use ($tuNgay, $denNgay) {
            return DB::table('DonHang')
                ->whereDate('NgayTao', '>=', $tuNgay)
                ->whereDate('NgayTao', '<=', $denNgay);
        };

        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ
        |--------------------------------------------------------------------------
        */

        $trangThaiHuy = ['DaHuy', 'Đã hủy'];
        $thongKe = [
            'doanhThu' => $ordersInPeriod()
                ->whereNotIn('TrangThai', $trangThaiHuy)
                ->sum('TongTien'),
            'tongDonHang' => $ordersInPeriod()->count(),
            'khachHangMoi' => DB::table('NguoiDung')
                ->where('VaiTro', 'KhachHang')
                ->whereDate('NgayTao', '>=', $tuNgay)
                ->whereDate('NgayTao', '<=', $denNgay)
                ->count(),
            'hoanTienChoXuLy' => DB::table('YeuCauHoanTien')
                ->whereIn('TrangThai', ['ChoXuLy', 'Chờ xử lý'])
                ->whereDate('NgayYeuCau', '>=', $tuNgay)
                ->whereDate('NgayYeuCau', '<=', $denNgay)
                ->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | TRẠNG THÁI ĐƠN HÀNG
        |--------------------------------------------------------------------------
        */

        $nhomTrangThai = [
            [
                'ten' => 'Đã hoàn thành',
                'class' => 'completed',
                'trangThai' => ['HoanThanh', 'DaGiao', 'Đã giao', 'Hoàn thành'],
            ],
            [
                'ten' => 'Đang giao',
                'class' => 'shipping',
                'trangThai' => ['DangGiao', 'DangGiaoHang', 'Đang giao hàng'],
            ],
            [
                'ten' => 'Chờ xác nhận',
                'class' => 'pending',
                'trangThai' => [
                    'MoiTao',
                    'ChoXacNhan',
                    'Chờ xác nhận',
                    'DaXacNhan',
                    'Đã xác nhận',
                ],
            ],
            [
                'ten' => 'Đã hủy',
                'class' => 'cancelled',
                'trangThai' => $trangThaiHuy,
            ],
            [
                'ten' => 'Trạng thái khác',
                'class' => 'other',
                'trangThai' => [],
            ],
        ];

        $soDonTheoTrangThai = $ordersInPeriod()
            ->select('TrangThai', DB::raw('COUNT(*) as tong'))
            ->groupBy('TrangThai')
            ->pluck('tong', 'TrangThai');
        $tongDonTheoTrangThai = (int) $soDonTheoTrangThai->sum();
        $trangThaiDonHang = [];
        $cacDoanBieuDo = [];
        $cacTrangThaiDaPhanLoai = collect($nhomTrangThai)
            ->where('class', '!=', 'other')
            ->flatMap(fn ($nhom) => $nhom['trangThai'])
            ->all();
        $gocBatDau = 0;

        foreach ($nhomTrangThai as $nhom) {
            $soDon = $nhom['trangThai']
                ? (int) $soDonTheoTrangThai->only($nhom['trangThai'])->sum()
                : (int) $soDonTheoTrangThai
                    ->reject(fn ($tong, $trangThai) => in_array(
                        $trangThai,
                        $cacTrangThaiDaPhanLoai,
                        true
                    ))
                    ->sum();
            $phanTram = $tongDonTheoTrangThai > 0
                ? $soDon / $tongDonTheoTrangThai * 100
                : 0;

            $trangThaiDonHang[] = [
                'ten' => $nhom['ten'],
                'phanTram' => round($phanTram),
                'class' => $nhom['class'],
            ];

            if ($phanTram > 0) {
                $gocKetThuc = $gocBatDau + ($phanTram / 100 * 360);
                $mau = [
                    'completed' => '#59A78E',
                    'shipping' => '#FFD554',
                    'pending' => '#FFAAAA',
                    'cancelled' => '#C73636',
                    'other' => '#CBD5E1',
                ][$nhom['class']];
                $cacDoanBieuDo[] = "{$mau} {$gocBatDau}deg {$gocKetThuc}deg";
                $gocBatDau = $gocKetThuc;
            }
        }

        $bieuDoTrangThai = $tongDonTheoTrangThai > 0
            ? 'conic-gradient('.implode(', ', $cacDoanBieuDo).')'
            : 'conic-gradient(#E5E7EB 0deg 360deg)';

        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM SẮP HẾT HÀNG
        |--------------------------------------------------------------------------
        */

        $sanPhamSapHet = DB::table('BienThe as bt')
            ->join('SanPham as sp', 'sp.SanPhamID', '=', 'bt.SanPhamID')
            ->whereRaw('bt.SoLuong <= COALESCE(bt.SoLuongTamGiu, 0) + sp.LuongTonKhoThap')
            ->select(
                'sp.TenSanPham as ten',
                'bt.MauSac',
                'bt.KichThuoc',
                DB::raw('CASE WHEN bt.SoLuong > COALESCE(bt.SoLuongTamGiu, 0)
                    THEN bt.SoLuong - COALESCE(bt.SoLuongTamGiu, 0) ELSE 0 END as soLuong')
            )
            ->orderBy('soLuong')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'ten' => $item->ten,
                'bienThe' => trim($item->MauSac.' / '.$item->KichThuoc, ' /'),
                'soLuong' => (int) $item->soLuong,
            ]);

        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM BÁN CHẠY
        |--------------------------------------------------------------------------
        */

        $sanPhamBanChay = DB::table('ChiTietDonHang as ct')
            ->join('DonHang as dh', 'dh.DonHangID', '=', 'ct.DonHangID')
            ->join('SanPham as sp', 'sp.SanPhamID', '=', 'ct.SanPhamID')
            ->whereDate('dh.NgayTao', '>=', $tuNgay)
            ->whereDate('dh.NgayTao', '<=', $denNgay)
            ->whereNotIn('dh.TrangThai', $trangThaiHuy)
            ->select(
                'sp.TenSanPham as ten',
                DB::raw('SUM(ct.SoLuong) as daBan'),
                DB::raw('SUM(ct.SoLuong * ct.GiaTaiThoiDiemMua) as doanhThu')
            )
            ->groupBy('sp.SanPhamID', 'sp.TenSanPham')
            ->orderByDesc('daBan')
            ->orderBy('sp.SanPhamID')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'ten' => $item->ten,
                'daBan' => (int) $item->daBan,
                'doanhThu' => (int) $item->doanhThu,
            ]);

        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM BÁN CHẬM
        |--------------------------------------------------------------------------
        */

        $soLuongBanTheoSanPham = DB::table('ChiTietDonHang as ct')
            ->join('DonHang as dh', 'dh.DonHangID', '=', 'ct.DonHangID')
            ->whereDate('dh.NgayTao', '>=', $tuNgay)
            ->whereDate('dh.NgayTao', '<=', $denNgay)
            ->whereNotIn('dh.TrangThai', $trangThaiHuy)
            ->select(
                'ct.SanPhamID',
                DB::raw('SUM(ct.SoLuong) as daBan'),
                DB::raw('SUM(ct.SoLuong * ct.GiaTaiThoiDiemMua) as doanhThu')
            )
            ->groupBy('ct.SanPhamID');

        $sanPhamBanCham = DB::table('SanPham as sp')
            ->leftJoinSub($soLuongBanTheoSanPham, 'sales', function ($join) {
                $join->on('sales.SanPhamID', '=', 'sp.SanPhamID');
            })
            ->select(
                'sp.TenSanPham as ten',
                DB::raw('COALESCE(sales.daBan, 0) as daBan'),
                DB::raw('COALESCE(sales.doanhThu, 0) as doanhThu')
            )
            ->orderBy('daBan')
            ->orderBy('sp.SanPhamID')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'ten' => $item->ten,
                'daBan' => (int) $item->daBan,
                'doanhThu' => (int) $item->doanhThu,
            ]);

        return view('admin.overview', [

            'tuNgay' => $tuNgay,

            'denNgay' => $denNgay,

            'thongKe' => $thongKe,

            'trangThaiDonHang' => $trangThaiDonHang,

            'bieuDoTrangThai' => $bieuDoTrangThai,

            'sanPhamSapHet' => $sanPhamSapHet,

            'sanPhamBanChay' => $sanPhamBanChay,

            'sanPhamBanCham' => $sanPhamBanCham,

        ]);
    }
}
