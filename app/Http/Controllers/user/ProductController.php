<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
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
                        ->where('SanPham.TenSanPham', 'like', '%' . $keyword . '%')
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
            compact('keyword', 'products', 'favoriteProductIds')
        );
    }
}