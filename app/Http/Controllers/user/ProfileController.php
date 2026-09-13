<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        // Kiểm tra đã đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        // Lấy thông tin người dùng đang đăng nhập
        $user = DB::table('NguoiDung')
            ->where('NguoiDungID', session('NguoiDungID'))
            ->first();

        if (!$user) {
            session()->forget('NguoiDungID');

            return redirect()->route('login');
        }

        // Lấy danh mục cho navbar
        $danhMucs = DB::table('DanhMuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view('user.account.profile', compact(
            'user',
            'danhMucs'
        ));
    }
}