<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // TRANG ĐĂNG NHẬP
    public function showLogin()
    {
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view('user.account.login', compact('danhMucs'));
    }

    // TRANG ĐĂNG KÝ
    public function showRegister()
    {
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view('user.account.register', compact('danhMucs'));
    }

    public function showForgotPassword()
    {
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view('user.account.forgot-password', compact('danhMucs'));
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'email.email' => 'Email không đúng định dạng.',
        ]);

        $emailExists = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->exists();

        if (!$emailExists) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email chưa được đăng ký.']);
        }

        return back()->with('success', 'Yêu cầu khôi phục mật khẩu đã được ghi nhận.');
    }

    // XỬ LÝ ĐĂNG KÝ
    public function register(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                'regex:/^[\p{L}]+(?:\s+[\p{L}]+)*$/u',
            ],
            'email' => 'required|email:rfc,dns|max:100',
            'phone' => [
                'required',
                'digits:10',
                'regex:/^0[0-9]{9}$/',
            ],
            'birthday' => 'required|date|before_or_equal:today',
            'password' => 'required|string|min:8|max:255|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.regex' => 'Họ và tên chỉ được chứa chữ cái và khoảng trắng, không được có số.',
            'email.required' => 'Vui lòng nhập Email.',
            'email.email' => 'Email không đúng định dạng.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.digits' => 'Số điện thoại phải gồm đúng 10 số.',
            'phone.regex' => 'Số điện thoại phải bắt đầu bằng số 0.',
            'birthday.required' => 'Vui lòng chọn ngày sinh.',
            'birthday.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        // Kiểm tra Email đã tồn tại
        $emailExists = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->exists();

        if ($emailExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Email này đã được đăng ký.'
                ]);
        }

        // Kiểm tra số điện thoại đã tồn tại
        $phoneExists = DB::table('NguoiDung')
            ->where('SoDienThoai', $request->phone)
            ->exists();

        if ($phoneExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'phone' => 'Số điện thoại này đã được đăng ký.'
                ]);
        }

        // Tách Họ và Tên
        $hoTen = trim($request->name);
        $parts = preg_split('/\s+/', $hoTen);

        $ten = array_pop($parts);
        $ho = implode(' ', $parts);

        // Nếu chỉ nhập một từ thì đưa vào cột Tên
        if ($ho === '') {
            $ho = '';
        }

        // Lưu tài khoản
        DB::table('NguoiDung')->insert([
            'Ho' => $ho,
            'Ten' => $ten,
            'Email' => $request->email,
            'SoDienThoai' => $request->phone,
            'NgaySinh' => $request->birthday,
            'MatKhau' => Hash::make($request->password),
            'VaiTro' => 'KhachHang',
            'TrangThai' => 'HoatDong',
            'NgayTao' => now(),
            'NgayCapNhat' => now(),
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Đăng ký tài khoản thành công. Vui lòng đăng nhập.');
    }


    // XỬ LÝ ĐĂNG NHẬP
// XỬ LÝ ĐĂNG NHẬP
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ], [
        'email.required' => 'Vui lòng nhập Email.',
        'email.email' => 'Email không đúng định dạng.',
        'password.required' => 'Vui lòng nhập mật khẩu.',
    ]);

    // Tìm tài khoản theo Email
    $nguoiDung = DB::table('NguoiDung')
        ->where('Email', $request->email)
        ->first();

    // Email không tồn tại
    if (!$nguoiDung) {
        return back()
            ->withInput()
            ->withErrors([
                    'password' => 'Email hoặc mật khẩu không chính xác.'
            ]);
    }

    // Kiểm tra mật khẩu dạng thường
    if ($nguoiDung->MatKhau !== $request->password) {
        return back()
            ->withInput()
            ->withErrors([
                    'password' => 'Email hoặc mật khẩu không chính xác.'
            ]);
    }

    // Kiểm tra tài khoản có đang hoạt động không
    if ($nguoiDung->TrangThai !== 'HoatDong') {
        return back()
            ->withInput()
            ->withErrors([
                    'password' => 'Tài khoản của bạn hiện không hoạt động.'
            ]);
    }

    // Lưu thông tin người dùng vào Session
    session([
        'NguoiDungID' => $nguoiDung->NguoiDungID,
        'Ho' => $nguoiDung->Ho,
        'Ten' => $nguoiDung->Ten,
        'Email' => $nguoiDung->Email,
        'SoDienThoai' => $nguoiDung->SoDienThoai,
        'NgaySinh' => $nguoiDung->NgaySinh,
        'VaiTro' => $nguoiDung->VaiTro,
    ]);

    // Đăng nhập thành công
    return redirect()
        ->route('home')
        ->with('success', 'Đăng nhập thành công.');
}
}