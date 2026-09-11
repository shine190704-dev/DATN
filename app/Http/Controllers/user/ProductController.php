<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index()
    {
        $products = DB::table('SanPham')
            ->leftJoin('HinhAnhSanPham', function ($join) {
                $join->on(
                    'SanPham.SanPhamID',
                    '=',
                    'HinhAnhSanPham.SanPhamID'
                )
                ->where('HinhAnhSanPham.AnhDaiDien', 1);
            })
            ->where('SanPham.TrangThai', 'HoatDong')
            ->orderByDesc('SanPham.DaBan')
            ->limit(8)
            ->select(
                'SanPham.*',
                'HinhAnhSanPham.DuongDanAnh as HinhAnh'
            )
            ->get();

        return view(
            'user.product.index',
            compact('products')
        );
    }
}