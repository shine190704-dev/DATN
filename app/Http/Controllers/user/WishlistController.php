<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    // =========================================================
    // TRANG SẢN PHẨM YÊU THÍCH
    // =========================================================

    public function index()
    {
        // Kiểm tra người dùng đã đăng nhập chưa
        if (!session()->has('NguoiDungID')) {
            return redirect()
                ->route('login')
                ->with('error', 'Vui lòng đăng nhập để xem sản phẩm yêu thích.');
        }

        $nguoiDungID = session('NguoiDungID');

        // Lấy danh mục để hiển thị Navbar
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        // Lấy các sản phẩm đã yêu thích
        $products = DB::table('danhsachyeuthich')
            ->join(
                'SanPham',
                'danhsachyeuthich.SanPhamID',
                '=',
                'SanPham.SanPhamID'
            )
            ->where(
                'danhsachyeuthich.NguoiDungID',
                $nguoiDungID
            )
            ->where(
                'SanPham.TrangThai',
                'HoatDong'
            )
            ->select(
                'SanPham.*',
                'danhsachyeuthich.DanhSachYeuThichID',
                'danhsachyeuthich.NgayTao as NgayYeuThich'
            )
            ->selectSub(
                $this->subQueryHinhAnh(),
                'HinhAnh'
            )
            ->orderByDesc('danhsachyeuthich.NgayTao')
            ->get();

        $favoriteProductIds = $products->pluck('SanPhamID');

        return view(
            'user.products.product-favorite',
            compact(
                'danhMucs',
                'products',
                'favoriteProductIds'
            )
        );
    }


    // =========================================================
    // THÊM / BỎ SẢN PHẨM YÊU THÍCH
    // =========================================================

    public function toggle(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!session()->has('NguoiDungID')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng chức năng yêu thích.'
            ], 401);
        }

        // Kiểm tra SanPhamID
        $request->validate([
            'SanPhamID' => 'required|integer'
        ]);

        $nguoiDungID = session('NguoiDungID');
        $sanPhamID = $request->SanPhamID;

        // Kiểm tra sản phẩm có tồn tại và đang hoạt động không
        $sanPham = DB::table('SanPham')
            ->where('SanPhamID', $sanPhamID)
            ->where('TrangThai', 'HoatDong')
            ->first();

        if (!$sanPham) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại hoặc đã ngừng bán.'
            ], 404);
        }

        // Kiểm tra sản phẩm đã được yêu thích chưa
        $wishlist = DB::table('danhsachyeuthich')
            ->where('NguoiDungID', $nguoiDungID)
            ->where('SanPhamID', $sanPhamID)
            ->first();

        // =====================================================
        // ĐÃ CÓ → BỎ YÊU THÍCH
        // =====================================================

        if ($wishlist) {

            DB::table('danhsachyeuthich')
                ->where(
                    'DanhSachYeuThichID',
                    $wishlist->DanhSachYeuThichID
                )
                ->delete();

            return response()->json([
                'success' => true,
                'favorite' => false,
                'message' => 'Đã bỏ sản phẩm khỏi danh sách yêu thích.'
            ]);
        }


        // =====================================================
        // CHƯA CÓ → THÊM YÊU THÍCH
        // =====================================================

        DB::table('danhsachyeuthich')->insert([
            'NgayTao' => now(),
            'NgayCapNhat' => now(),
            'NguoiDungID' => $nguoiDungID,
            'SanPhamID' => $sanPhamID,
        ]);

        return response()->json([
            'success' => true,
            'favorite' => true,
            'message' => 'Đã thêm sản phẩm vào danh sách yêu thích.'
        ]);
    }


    // =========================================================
    // LẤY ẢNH ĐẠI DIỆN SẢN PHẨM
    // =========================================================

    private function subQueryHinhAnh()
    {
        return function ($query) {

            $query->from('HinhAnhSanPham')
                ->select('DuongDanAnh')
                ->whereColumn(
                    'HinhAnhSanPham.SanPhamID',
                    'SanPham.SanPhamID'
                )
                ->orderByDesc('AnhDaiDien')
                ->orderBy('HinhAnhSanPhamID')
                ->limit(1);
        };
    }
}