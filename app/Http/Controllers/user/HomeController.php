<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Danh mục cho navbar
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();


        // =========================================
        // SẢN PHẨM NỔI BẬT
        // 4 sản phẩm bán nhiều nhất
        // =========================================

        $sanPhamNoiBat = DB::table('SanPham')
            ->where('SanPham.TrangThai', 'HoatDong')
            ->orderByDesc('SanPham.DaBan')
            ->limit(8)
            ->select('SanPham.*')

            ->selectSub(function ($query) {

                $query->from('HinhAnhSanPham')
                    ->select('DuongDanAnh')
                    ->whereColumn(
                        'HinhAnhSanPham.SanPhamID',
                        'SanPham.SanPhamID'
                    )
                    ->orderByDesc('AnhDaiDien')
                    ->orderBy('HinhAnhSanPhamID')
                    ->limit(1);

            }, 'HinhAnh')

            ->get();


        // =========================================
        // DÀNH CHO BẠN
        // 4 sản phẩm bán ít hơn
        // =========================================

        $sanPhamDanhChoBan = DB::table('SanPham')
            ->where('SanPham.TrangThai', 'HoatDong')
            ->orderBy('SanPham.DaBan', 'asc')
            ->limit(4)
            ->select('SanPham.*')

            ->selectSub(function ($query) {

                $query->from('HinhAnhSanPham')
                    ->select('DuongDanAnh')
                    ->whereColumn(
                        'HinhAnhSanPham.SanPhamID',
                        'SanPham.SanPhamID'
                    )
                    ->orderByDesc('AnhDaiDien')
                    ->orderBy('HinhAnhSanPhamID')
                    ->limit(1);

            }, 'HinhAnh')

            ->get();


        return view(
            'user.homepage.home',
            compact(
                'danhMucs',
                'sanPhamNoiBat',
                'sanPhamDanhChoBan'
            )
        );
    }
}