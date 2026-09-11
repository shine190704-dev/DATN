<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function show($id)
    {
        $danhMuc = DB::table('danhmuc')
            ->where('DanhMucID', $id)
            ->where('TrangThai', 'HoatDong')
            ->first();

        $products = DB::table('SanPham')
            ->where('DanhMucID', $id)
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view('user.product.category', compact('danhMuc', 'products'));
    }
}