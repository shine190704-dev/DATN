<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    // Cột lưu mật khẩu trong bảng NguoiDung
    private const PASSWORD_COLUMN = 'MatKhau';


    // =========================
    // TRANG ĐỔI MẬT KHẨU
    // =========================

    public function index()
    {
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = session('NguoiDungID');

        // Lấy thông tin người dùng
        $user = DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if (!$user) {
            return redirect()->route('login');
        }

        // Lấy danh mục cho navbar
        $danhMucs = DB::table('DanhMuc')
            ->get();

        return view(
            'user.account.change-password',
            compact('user', 'danhMucs')
        );
    }


    // =========================
    // CẬP NHẬT MẬT KHẨU
    // =========================

    public function update(Request $request)
    {
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        // KIỂM TRA DỮ LIỆU
        $request->validate(
            [
                'current_password' => ['required'],

                'new_password' => [
                    'required',
                    'min:6',
                    'confirmed',
                    'different:current_password',
                ],
                
            ],
            [
                'current_password.required'
                    => 'Vui lòng nhập mật khẩu hiện tại.',

                'new_password.required'
                    => 'Vui lòng nhập mật khẩu mới.',

                'new_password.min'
                    => 'Mật khẩu mới phải có ít nhất 6 ký tự.',

                'new_password.regex'
                    => 'Mật khẩu mới phải gồm cả chữ và số.',

                'new_password.confirmed'
                    => 'Mật khẩu nhập lại không khớp.',

                'new_password.different'
                    => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
            ]
        );


        $nguoiDungID = session('NguoiDungID');

        $column = self::PASSWORD_COLUMN;


        // LẤY USER
        $user = DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if (!$user) {
            return redirect()->route('login');
        }


        // =========================
        // KIỂM TRA MẬT KHẨU HIỆN TẠI
        // =========================

        try {

            $valid = Hash::check(
                $request->current_password,
                $user->{$column}
            );

        } catch (\RuntimeException $e) {

            // Mật khẩu trong DB không đúng dạng hash
            $valid = false;

        }


        if (!$valid) {

            return back()->withErrors([
                'current_password'
                    => 'Mật khẩu hiện tại không đúng.',
            ]);

        }


        // =========================
        // CẬP NHẬT MẬT KHẨU MỚI
        // =========================

        DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->update([
                $column => Hash::make(
                    $request->new_password
                ),
            ]);


        // Đổi session ID để tăng bảo mật
        $request->session()->regenerate();


        // =========================
        // THÔNG BÁO THÀNH CÔNG
        // =========================

        return back()->with(
            'success',
            'Đổi mật khẩu thành công.'
        );
    }
}