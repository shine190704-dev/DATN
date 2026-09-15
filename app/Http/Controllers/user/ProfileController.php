<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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


    public function edit()
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

        return view('user.account.profile-edit', compact(
            'user',
            'danhMucs'
        ));
    }

    public function update(Request $request)
{
    // Kiểm tra đăng nhập
    if (!session()->has('NguoiDungID')) {
        return redirect()->route('login');
    }

    $userId = session('NguoiDungID');

    // Kiểm tra người dùng tồn tại
    $user = DB::table('NguoiDung')
        ->where('NguoiDungID', $userId)
        ->first();

    if (!$user) {
        session()->forget('NguoiDungID');

        return redirect()->route('login');
    }

    // Kiểm tra dữ liệu
    $validated = $request->validate([
        'HoTen' => [
            'required',
            'string',
            'max:200',
            'regex:/^[\p{L}\s]+$/u',
        ],

        'SoDienThoai' => [
            'required',
            'regex:/^0[0-9]{9}$/',
        ],

        'Email' => [
            'required',
            'email',
            'max:255',
        ],

        'NgaySinh' => [
            'nullable',
            'date',
            'before_or_equal:today',
        ],
    ], [
        'HoTen.required' => 'Vui lòng nhập họ và tên.',
        'HoTen.regex' => 'Họ và tên chỉ được chứa chữ cái và khoảng trắng.',
        'HoTen.max' => 'Họ và tên không được vượt quá 200 ký tự.',

        'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
        'SoDienThoai.regex' => 'Số điện thoại phải có 10 chữ số và bắt đầu bằng 0.',

        'Email.required' => 'Vui lòng nhập email.',
        'Email.email' => 'Email không đúng định dạng.',
        'Email.max' => 'Email không được vượt quá 255 ký tự.',

        'NgaySinh.date' => 'Ngày sinh không hợp lệ.',
        'NgaySinh.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',
    ]);

    // Chuẩn hóa họ tên
    $hoTen = trim($validated['HoTen']);
    $hoTen = preg_replace('/\s+/', ' ', $hoTen);

    // Tách họ và tên
    $parts = explode(' ', $hoTen);

    $ten = array_pop($parts);
    $ho = implode(' ', $parts);

    // Cập nhật
    DB::table('NguoiDung')
        ->where('NguoiDungID', $userId)
        ->update([
            'Ho' => $ho,
            'Ten' => $ten,
            'SoDienThoai' => $validated['SoDienThoai'],
            'Email' => $validated['Email'],
            'NgaySinh' => $validated['NgaySinh'] ?? null,
        ]);

    return redirect()
        ->route('profile')
        ->with('success', 'Cập nhật thông tin thành công.');
}


}