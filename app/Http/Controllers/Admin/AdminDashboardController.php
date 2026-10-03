<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Kiểm tra đăng nhập Admin/Nhân viên
        if (!$request->session()->has('AdminNguoiDungID')) {
            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'email' => 'Vui lòng đăng nhập để truy cập khu vực quản trị.',
                ]);
        }

        // Kiểm tra quyền
        $vaiTro = $request->session()->get('AdminVaiTro');

        if (!in_array($vaiTro, ['Admin', 'NhanVien'])) {
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

        return view('admin.dashboard');
    }
}