<?php

namespace App\Http\Controllers\Admin;


use App\Models\BienThe;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\NguoiDung;
use App\Models\SanPham;
use App\Models\YeuCauHoanTien;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminOverviewController extends Controller
{
    /** Ngưỡng "sắp hết" mặc định khi sản phẩm chưa đặt LuongTonKhoThap (hàng bán chạy/chậm đặt 8-10 trong DB). */
    private const NGUONG_SAP_HET_MAC_DINH = 5;

    /** Giá trị TrangThai của sản phẩm đang bán -> chỉnh lại cho đúng dữ liệu của bạn. */
    private const SAN_PHAM_DANG_BAN = 'HoatDong';

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA ĐĂNG NHẬP + QUYỀN
        |--------------------------------------------------------------------------
        */

        if ($redirect = $this->kiemTraTruyCap($request)) {
            return $redirect;
        }

        $vaiTro = $request->session()->get('AdminVaiTro');
        $laAdmin = $vaiTro === 'Admin';

        /*
        |--------------------------------------------------------------------------
        | NGÀY THỐNG KÊ
        |--------------------------------------------------------------------------
        */

        $macDinhTu = now()->subDays(29)->toDateString();
        $macDinhDen = now()->toDateString();
        $ngayCuoiNam = now()->endOfYear()->toDateString();

        $tuNgayNhap = $request->input('tu_ngay');
        $denNgayNhap = $request->input('den_ngay');

        $dateValidator = Validator::make([
            'tu_ngay' => $tuNgayNhap,
            'den_ngay' => $denNgayNhap,
        ], [
            'tu_ngay' => ['nullable', 'date_format:d/m/Y'],
            'den_ngay' => ['nullable', 'date_format:d/m/Y'],
        ]);

        $dateRangeError = null;

        if ($dateValidator->fails()) {
            $dateRangeError = 'Khoảng thời gian không hợp lệ';
            $tuNgay = $dateValidator->errors()->has('tu_ngay')
                ? $macDinhTu
                : ($tuNgayNhap
                    ? Carbon::createFromFormat('d/m/Y', $tuNgayNhap)->format('Y-m-d')
                    : $macDinhTu);
            $denNgay = $dateValidator->errors()->has('den_ngay')
                ? $macDinhDen
                : ($denNgayNhap
                    ? Carbon::createFromFormat('d/m/Y', $denNgayNhap)->format('Y-m-d')
                    : $macDinhDen);
        } else {
            // Nếu chỉ nhập một ngày, mặc định ngày còn lại để tạo khoảng lọc.
            $denNgay = $denNgayNhap
                ? Carbon::createFromFormat('d/m/Y', $denNgayNhap)->format('Y-m-d')
                : $macDinhDen;
            $tuNgay = $tuNgayNhap
                ? Carbon::createFromFormat('d/m/Y', $tuNgayNhap)->format('Y-m-d')
                : Carbon::parse($denNgay)->subDays(29)->toDateString();

            if ($tuNgay >= $denNgay || $tuNgay > $ngayCuoiNam || $denNgay > $ngayCuoiNam) {
                $dateRangeError = 'Khoảng thời gian không hợp lệ';
            }
        }

        if ($dateRangeError) {
            // Khoảng rỗng để không hiển thị thống kê sai khi bộ lọc ngày không hợp lệ.
            $tuThoiDiem = '1970-01-01 00:00:00';
            $denThoiDiemLoaiTru = '1970-01-01 00:00:00';
        } else {
            $tuThoiDiem = $tuNgay.' 00:00:00';
            // Mốc kết thúc loại trừ để tính trọn ngày, kể cả bản ghi có phần giây lẻ.
            $denThoiDiemLoaiTru = Carbon::parse($denNgay)->addDay()->startOfDay()->toDateTimeString();
        }

        $ordersInPeriod = static function () use ($tuThoiDiem, $denThoiDiemLoaiTru) {
            return DonHang::query()->from('DonHang')
                ->where('NgayTao', '>=', $tuThoiDiem)
                ->where('NgayTao', '<', $denThoiDiemLoaiTru);
        };

        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ
        |--------------------------------------------------------------------------
        */

        $trangThaiHuy = ['DaHuy', 'Đã hủy'];
        $trangThaiHoanThanh = ['HoanThanh', 'DaGiao', 'Đã giao', 'Hoàn thành'];

        $thongKe = [
            // Doanh thu chỉ tính đơn đã hoàn thành; nhân viên không xem được.
            'doanhThu' => $laAdmin
                ? (int) $ordersInPeriod()
                    ->whereIn('TrangThai', $trangThaiHoanThanh)
                    ->sum('TongTien')
                : null,
            'tongDonHang' => $ordersInPeriod()->count(),
            'khachHangMoi' => NguoiDung::query()->from('NguoiDung')
                ->where('VaiTro', 'KhachHang')
                ->where('NgayTao', '>=', $tuThoiDiem)
                ->where('NgayTao', '<', $denThoiDiemLoaiTru)
                ->count(),
            // Việc cần xử lý: đếm tất cả, không lọc theo ngày.
            'hoanTienChoXuLy' => YeuCauHoanTien::query()->from('YeuCauHoanTien')
                ->whereIn('TrangThai', ['ChoXuLy', 'Chờ xử lý'])
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
                'trangThai' => $trangThaiHoanThanh,
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
        | SẢN PHẨM SẮP HẾT HÀNG / HẾT HÀNG
        |--------------------------------------------------------------------------
        | Tồn khả dụng = SoLuong - SoLuongTamGiu.
        | - Hết hàng : tồn khả dụng = 0
        | - Sắp hết  : tồn khả dụng <= LuongTonKhoThap (mặc định 5)
        */

        $sanPhamSapHet = BienThe::query()->from('BienThe as bt')
            ->join('SanPham as sp', 'sp.SanPhamID', '=', 'bt.SanPhamID')
            ->where('sp.TrangThai', self::SAN_PHAM_DANG_BAN)
            ->whereRaw(
                '(bt.SoLuong - COALESCE(bt.SoLuongTamGiu, 0)) <= COALESCE(sp.LuongTonKhoThap, ?)',
                [self::NGUONG_SAP_HET_MAC_DINH]
            )
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
                'hetHang' => (int) $item->soLuong === 0,
            ]);

        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM BÁN CHẠY
        |--------------------------------------------------------------------------
        */

        $sanPhamBanChay = ChiTietDonHang::query()->from('ChiTietDonHang as ct')
            ->join('DonHang as dh', 'dh.DonHangID', '=', 'ct.DonHangID')
            ->join('SanPham as sp', 'sp.SanPhamID', '=', 'ct.SanPhamID')
            ->where('dh.NgayTao', '>=', $tuThoiDiem)
            ->where('dh.NgayTao', '<', $denThoiDiemLoaiTru)
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
                'doanhThu' => $laAdmin ? (int) $item->doanhThu : null,
            ]);

        /*
        |--------------------------------------------------------------------------
        | SẢN PHẨM BÁN CHẬM
        |--------------------------------------------------------------------------
        | Chỉ xét sản phẩm đang bán, đã tồn tại trước kỳ thống kê và còn tồn khả dụng.
        */

        $soLuongBanTheoSanPham = ChiTietDonHang::query()->from('ChiTietDonHang as ct')
            ->join('DonHang as dh', 'dh.DonHangID', '=', 'ct.DonHangID')
            ->where('dh.NgayTao', '>=', $tuThoiDiem)
            ->where('dh.NgayTao', '<', $denThoiDiemLoaiTru)
            ->whereNotIn('dh.TrangThai', $trangThaiHuy)
            ->select(
                'ct.SanPhamID',
                DB::raw('SUM(ct.SoLuong) as daBan'),
                DB::raw('SUM(ct.SoLuong * ct.GiaTaiThoiDiemMua) as doanhThu')
            )
            ->groupBy('ct.SanPhamID');

        $sanPhamBanCham = SanPham::query()->from('SanPham as sp')
            ->leftJoinSub($soLuongBanTheoSanPham, 'sales', function ($join) {
                $join->on('sales.SanPhamID', '=', 'sp.SanPhamID');
            })
            ->where('sp.TrangThai', self::SAN_PHAM_DANG_BAN)
            ->where('sp.NgayTao', '<', $tuThoiDiem)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('BienThe as bt')
                    ->whereColumn('bt.SanPhamID', 'sp.SanPhamID')
                    ->whereRaw('bt.SoLuong > COALESCE(bt.SoLuongTamGiu, 0)');
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
                'doanhThu' => $laAdmin ? (int) $item->doanhThu : null,
            ]);

        return view('admin.overview', [

            'tuNgay' => $tuNgay,

            'denNgay' => $denNgay,

            'tuNgayForm' => $tuNgayNhap ?? Carbon::parse($tuNgay)->format('d/m/Y'),

            'denNgayForm' => $denNgayNhap ?? Carbon::parse($denNgay)->format('d/m/Y'),

            'dateRangeError' => $dateRangeError,

            'laAdmin' => $laAdmin,

            'thongKe' => $thongKe,

            'trangThaiDonHang' => $trangThaiDonHang,

            'bieuDoTrangThai' => $bieuDoTrangThai,

            'sanPhamSapHet' => $sanPhamSapHet,

            'sanPhamBanChay' => $sanPhamBanChay,

            'sanPhamBanCham' => $sanPhamBanCham,

        ]);
    }

    /**
     * Trả về redirect nếu chưa đăng nhập / không đủ quyền / tài khoản bị khóa, ngược lại trả null.
     */
    private function kiemTraTruyCap(Request $request)
    {
        $session = $request->session();

        if (! $session->has('AdminNguoiDungID')) {
            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => 'Vui lòng đăng nhập để truy cập khu vực quản trị.',
                ]);
        }

        $vaiTro = $session->get('AdminVaiTro');

        $trangThai = NguoiDung::query()->from('NguoiDung')
            ->where('NguoiDungID', $session->get('AdminNguoiDungID'))
            ->value('TrangThai');

        if (! in_array($vaiTro, ['Admin', 'NhanVien'], true) || $trangThai !== 'HoatDong') {
            $session->forget([
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

        return null;
    }
}
