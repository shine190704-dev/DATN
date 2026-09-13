<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // =========================================
        // DANH MỤC CHO NAVBAR
        // =========================================
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();


        // =========================================
        // SẢN PHẨM NỔI BẬT
        // 8 sản phẩm bán nhiều nhất
        // =========================================
        $sanPhamNoiBat = DB::table('SanPham')
            ->where('SanPham.TrangThai', 'HoatDong')

            // DaBan cao nhất → bán nhiều nhất
            ->orderByDesc('SanPham.DaBan')

            // Nếu cùng DaBan → ưu tiên sản phẩm có ID nhỏ hơn
            ->orderBy('SanPham.SanPhamID', 'asc')

            ->limit(8)

            ->select('SanPham.*')

            // Lấy ảnh đại diện
            ->selectSub(
                $this->subQueryHinhAnh(),
                'HinhAnh'
            )

            ->get();


        // =========================================
        // LẤY ID SẢN PHẨM NỔI BẬT
        // Để DÀNH CHO BẠN không bị trùng
        // =========================================
        $idDaLayNoiBat = $sanPhamNoiBat
            ->pluck('SanPhamID')
            ->toArray();


        // =========================================
        // DÀNH CHO BẠN
        // 8 sản phẩm bán ít nhất
        // Không trùng với SẢN PHẨM NỔI BẬT
        // =========================================
        $sanPhamDanhChoBan = DB::table('SanPham')
            ->where('SanPham.TrangThai', 'HoatDong')

            // Loại những sản phẩm đã nằm trong Nổi bật
            ->whereNotIn(
                'SanPham.SanPhamID',
                $idDaLayNoiBat
            )

            // DaBan thấp nhất → bán ít nhất
            ->orderBy('SanPham.DaBan', 'asc')

            // Nếu cùng DaBan → ưu tiên ID nhỏ hơn
            ->orderBy('SanPham.SanPhamID', 'asc')

            ->limit(8)

            ->select('SanPham.*')

            // Lấy ảnh đại diện
            ->selectSub(
                $this->subQueryHinhAnh(),
                'HinhAnh'
            )

            ->get();

        $favoriteProductIds = session()->has('NguoiDungID')
            ? DB::table('danhsachyeuthich')
                ->where('NguoiDungID', session('NguoiDungID'))
                ->pluck('SanPhamID')
            : collect();


        // =========================================
        // TRẢ DỮ LIỆU VỀ HOMEPAGE
        // =========================================
        return view(
            'user.homepage.home',
            compact(
                'danhMucs',
                'sanPhamNoiBat',
                'sanPhamDanhChoBan',
                'favoriteProductIds'
            )
        );
    }


    // =========================================
    // LẤY ẢNH ĐẠI DIỆN SẢN PHẨM
    // =========================================
    private function subQueryHinhAnh()
    {
        return function ($query) {

            $query->from('HinhAnhSanPham')
                ->select('DuongDanAnh')

                ->whereColumn(
                    'HinhAnhSanPham.SanPhamID',
                    'SanPham.SanPhamID'
                )

                // Ưu tiên ảnh đại diện
                ->orderByDesc('AnhDaiDien')

                // Nếu có nhiều ảnh cùng trạng thái
                // → lấy ảnh được thêm trước
                ->orderBy('HinhAnhSanPhamID')

                ->limit(1);
        };
    }
}