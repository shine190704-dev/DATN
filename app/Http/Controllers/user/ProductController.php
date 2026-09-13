<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // =========================
    // TÌM KIẾM SẢN PHẨM
    // =========================
    public function search(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        $products = DB::table('SanPham')
            ->leftJoin('HinhAnhSanPham', function ($join) {
                $join->on(
                    'SanPham.SanPhamID',
                    '=',
                    'HinhAnhSanPham.SanPhamID'
                )->where('HinhAnhSanPham.AnhDaiDien', 1);
            })
            ->where('SanPham.TrangThai', 'HoatDong')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($productQuery) use ($keyword) {
                    $productQuery
                        ->where(
                            'SanPham.TenSanPham',
                            'like',
                            '%' . $keyword . '%'
                        )
                        ->orWhere('SanPham.SanPhamID', $keyword);
                });
            })
            ->orderByDesc('SanPham.DaBan')
            ->select(
                'SanPham.*',
                'HinhAnhSanPham.DuongDanAnh as HinhAnh'
            )
            ->get();

        $favoriteProductIds = session()->has('NguoiDungID')
            ? DB::table('danhsachyeuthich')
                ->where('NguoiDungID', session('NguoiDungID'))
                ->pluck('SanPhamID')
            : collect();

        return view(
            'user.products.product-search',
            compact(
                'keyword',
                'products',
                'favoriteProductIds'
            )
        );
    }


    // =========================
    // CHI TIẾT SẢN PHẨM
    // =========================
    public function detail($id)
    {
        // Lấy thông tin sản phẩm
        $product = DB::table('SanPham')
            ->where('SanPhamID', $id)
            ->where('TrangThai', 'HoatDong')
            ->first();

        // Không tìm thấy sản phẩm
        if (!$product) {
            abort(404);
        }

        // Lấy tất cả hình ảnh của sản phẩm
        $images = DB::table('HinhAnhSanPham')
            ->where('SanPhamID', $id)
            ->orderByDesc('AnhDaiDien')
            ->orderBy('HinhAnhSanPhamID')
            ->get();

        // Lấy danh mục cho navbar
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view(
            'user.products.product-detail',
            compact(
                'product',
                'images',
                'danhMucs'
            )
        );
    }
}