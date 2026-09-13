<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // =========================================================
    // TRANG ĐĂNG NHẬP
    // =========================================================
    public function showLogin()
    {
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view(
            'user.account.login',
            compact('danhMucs')
        );
    }


    // =========================================================
    // TRANG ĐĂNG KÝ
    // =========================================================
    public function showRegister()
    {
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view(
            'user.account.register',
            compact('danhMucs')
        );
    }


    // =========================================================
    // TRANG QUÊN MẬT KHẨU
    // =========================================================
    public function showForgotPassword()
    {
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view(
            'user.account.forgot-password',
            compact('danhMucs')
        );
    }


    // =========================================================
    // GỬI LINK ĐẶT LẠI MẬT KHẨU QUA EMAIL
    // =========================================================
    public function sendResetLink(Request $request)
    {
        // Kiểm tra Email
        $request->validate([
            'email' => 'required|email:rfc,dns',
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'email.email' => 'Email không đúng định dạng.',
        ]);


        // Thông báo trả về cho người dùng luôn giống nhau,
        // dù email có tồn tại hay không — tránh lộ email nào
        // đã đăng ký trong hệ thống (user enumeration).
        $thongBaoChung = 'Nếu email tồn tại trong hệ thống, '
            . 'liên kết đặt lại mật khẩu đã được gửi đến hộp thư của bạn.';


        // Tìm tài khoản theo Email
        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->first();


        // Email chưa đăng ký, hoặc tài khoản không hoạt động
        // -> vẫn trả về thông báo thành công (không tiết lộ lý do thật)
        if (!$nguoiDung || $nguoiDung->TrangThai !== 'HoatDong') {
            return back()->with('success', $thongBaoChung);
        }


        // =====================================================
        // TẠO TOKEN RESET
        // =====================================================

        // Token gốc — chỉ gửi qua email, không lưu dạng này vào DB
        $token = Str::random(64);

        // Hash token trước khi lưu, để nếu DB bị lộ,
        // kẻ tấn công không thể dùng trực tiếp giá trị trong DB
        // để tự đặt lại mật khẩu người khác.
        $tokenHash = Hash::make($token);

        // Token có hiệu lực 15 phút
        $thoiGianHetHan = now()->addMinutes(15);


        // Lưu token (đã hash) vào database
        DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDung->NguoiDungID)
            ->update([
                'MaDatLaiMatKhau' => $tokenHash,
                'ThoiGianHetHanMaDatLaiMatKhau' => $thoiGianHetHan,
            ]);


        // =====================================================
        // TẠO LINK RESET (dùng token gốc, chưa hash)
        // =====================================================

        $resetLink = route('password.reset', [
            'token' => $token,
            'email' => $nguoiDung->Email,
        ]);


        // =====================================================
        // GỬI EMAIL THẬT
        // =====================================================

Mail::html(
    '
    <div style="
        background-color: #E8F4EF;
        border: 1px solid #59A78E;
        border-radius: 12px;
        max-width: 600px;
        margin: 30px auto;
        padding: 35px;
        font-family: Arial, sans-serif;
        color: #333333;
        box-sizing: border-box;
    ">

        <h2 style="
            margin: 0 0 25px;
            text-align: center;
            color: #59A78E;
            font-size: 26px;
        ">
            Dollie
        </h2>

        <h3 style="
            margin: 0 0 30px;
            text-align: center;
            color: #333333;
            font-size: 20px;
        ">
            Yêu cầu đặt lại mật khẩu
        </h3>

        <p>
            Xin chào ' . e($nguoiDung->Ho . ' ' . $nguoiDung->Ten) . ',
        </p>

        <p>
            Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản Dollie.
        </p>

        <p>
            Nhấn vào nút bên dưới để đặt lại mật khẩu:
        </p>

        <div style="
            text-align: center;
            margin: 30px 0;
        ">
            <a
                href="' . e($resetLink) . '"
                style="
                    display: inline-block;
                    padding: 13px 28px;
                    background-color: #FFD45A;
                    color: #333333;
                    text-decoration: none;
                    border-radius: 8px;
                    font-weight: bold;
                    font-size: 14px;
                "
            >
                ĐẶT LẠI MẬT KHẨU
            </a>
        </div>

        <p>
            Liên kết này có hiệu lực trong
            <strong>15 phút</strong>.
        </p>

        <p>
            Nếu bạn không yêu cầu đặt lại mật khẩu,
            vui lòng bỏ qua email này.
        </p>

        <hr style="
            border: none;
            border-top: 1px solid #59A78E;
            margin: 30px 0 20px;
        ">

        <p style="
            margin: 0;
            text-align: center;
            color: #777777;
            font-size: 13px;
        ">
            Trân trọng,<br>
            <strong>Dollie</strong>
        </p>

    </div>
    ',
    function ($message) use ($nguoiDung) {

        $message->to($nguoiDung->Email)
            ->subject('Dollie - Yêu cầu đặt lại mật khẩu');
    }
);





        // Thông báo thành công (cùng nội dung với trường hợp email không tồn tại)
        return back()->with('success', $thongBaoChung);
    }


    // =========================================================
    // TRANG ĐẶT LẠI MẬT KHẨU
    // =========================================================
    public function showResetPassword(Request $request, $token)
    {
        $email = $request->query('email');


        // Lấy tài khoản theo Email + còn hạn token trước,
        // vì token đã hash nên không thể where() trực tiếp bằng giá trị token gốc.
        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $email)
            ->whereNotNull('MaDatLaiMatKhau')
            ->where('ThoiGianHetHanMaDatLaiMatKhau', '>=', now())
            ->first();


        // Token không hợp lệ, hết hạn, hoặc không khớp
        if (!$nguoiDung || !Hash::check($token, $nguoiDung->MaDatLaiMatKhau)) {
            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'
                ]);
        }


        // Lấy danh mục để hiển thị Navbar
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();


        return view(
            'user.account.reset-password',
            compact(
                'danhMucs',
                'token',
                'email'
            )
        );
    }


    // =========================================================
    // XỬ LÝ ĐẶT LẠI MẬT KHẨU
    // =========================================================
    public function resetPassword(Request $request)
    {
        // Kiểm tra dữ liệu
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|max:255|confirmed',
        ], [
            'email.required' => 'Email không hợp lệ.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);


        // Tìm tài khoản theo Email, còn hạn token
        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->whereNotNull('MaDatLaiMatKhau')
            ->where('ThoiGianHetHanMaDatLaiMatKhau', '>=', now())
            ->first();


        // Token không hợp lệ, hết hạn, hoặc không khớp
        if (!$nguoiDung || !Hash::check($request->token, $nguoiDung->MaDatLaiMatKhau)) {
            return redirect()
                ->route('password.request')
                ->withErrors([
                    'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'
                ]);
        }


        // =====================================================
        // CẬP NHẬT MẬT KHẨU
        // =====================================================

        DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDung->NguoiDungID)
            ->update([
                'MatKhau' => Hash::make($request->password),

                // Xóa token sau khi sử dụng
                'MaDatLaiMatKhau' => null,
                'ThoiGianHetHanMaDatLaiMatKhau' => null,

                'NgayCapNhat' => now(),
            ]);


        // Chuyển về trang đăng nhập
        return redirect()
            ->route('login')
            ->with(
                'success',
                'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.'
            );
    }


    // =========================================================
    // XỬ LÝ ĐĂNG KÝ
    // =========================================================
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

            'birthday' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
            ],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',

            'name.regex' =>
                'Họ và tên chỉ được chứa chữ cái và khoảng trắng, không được có số.',

            'email.required' => 'Vui lòng nhập Email.',

            'email.email' =>
                'Email không đúng định dạng.',

            'phone.required' =>
                'Vui lòng nhập số điện thoại.',

            'phone.digits' =>
                'Số điện thoại phải gồm đúng 10 số.',

            'phone.regex' =>
                'Số điện thoại phải bắt đầu bằng số 0.',

            'birthday.required' =>
                'Vui lòng chọn ngày sinh.',

            'birthday.before_or_equal' =>
                'Ngày sinh không được lớn hơn ngày hiện tại.',

            'password.required' =>
                'Vui lòng nhập mật khẩu.',

            'password.min' =>
                'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);


        // =====================================================
        // KIỂM TRA EMAIL
        // =====================================================

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


        // =====================================================
        // KIỂM TRA SỐ ĐIỆN THOẠI
        // =====================================================

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


        // =====================================================
        // TÁCH HỌ VÀ TÊN
        // =====================================================

        $hoTen = trim($request->name);

        $parts = preg_split(
            '/\s+/',
            $hoTen
        );

        $ten = array_pop($parts);

        $ho = implode(
            ' ',
            $parts
        );


        // =====================================================
        // LƯU TÀI KHOẢN
        // =====================================================

        // Lưu ý: nếu 2 request đăng ký cùng email/SĐT gửi gần như
        // đồng thời, đảm bảo cột Email và SoDienThoai có UNIQUE
        // constraint ở tầng database để tránh trùng do race condition.
        try {
            DB::table('NguoiDung')->insert([
                'Ho' => $ho,

                'Ten' => $ten,

                'Email' => $request->email,

                'SoDienThoai' => $request->phone,

                'NgaySinh' => $request->birthday,

                'MatKhau' => Hash::make(
                    $request->password
                ),

                'VaiTro' => 'KhachHang',

                'TrangThai' => 'HoatDong',

                'NgayTao' => now(),

                'NgayCapNhat' => now(),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Bắt lỗi trùng dữ liệu do race condition (nếu có unique constraint ở DB)
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Email hoặc số điện thoại này đã được đăng ký.'
                ]);
        }


        // =====================================================
        // ĐĂNG KÝ THÀNH CÔNG
        // =====================================================

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Đăng ký tài khoản thành công. Vui lòng đăng nhập.'
            );
    }


    // =========================================================
    // XỬ LÝ ĐĂNG NHẬP
    // =========================================================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',

            'password' => 'required|string',
        ], [
            'email.required' =>
                'Vui lòng nhập Email.',

            'email.email' =>
                'Email không đúng định dạng.',

            'password.required' =>
                'Vui lòng nhập mật khẩu.',
        ]);


        // =====================================================
        // TÌM TÀI KHOẢN
        // =====================================================

        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->first();


        // Email không tồn tại
        if (!$nguoiDung) {
            return back()
                ->withInput()
                ->withErrors([
                    'password' =>
                        'Email hoặc mật khẩu không chính xác.'
                ]);
        }


        // =====================================================
        // KIỂM TRA MẬT KHẨU
        // =====================================================

        if (!Hash::check(
            $request->password,
            $nguoiDung->MatKhau
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'password' =>
                        'Email hoặc mật khẩu không chính xác.'
                ]);
        }


        // =====================================================
        // KIỂM TRA TRẠNG THÁI
        // =====================================================

        if ($nguoiDung->TrangThai !== 'HoatDong') {
            return back()
                ->withInput()
                ->withErrors([
                    'password' =>
                        'Tài khoản của bạn hiện không hoạt động.'
                ]);
        }


        // =====================================================
        // ĐỔI SESSION ID TRƯỚC KHI LƯU DỮ LIỆU ĐĂNG NHẬP
        // (chống session fixation attack)
        // =====================================================

        $request->session()->regenerate();


        // =====================================================
        // LƯU SESSION
        // =====================================================

        session([
            'NguoiDungID' => $nguoiDung->NguoiDungID,

            'Ho' => $nguoiDung->Ho,

            'Ten' => $nguoiDung->Ten,

            'Email' => $nguoiDung->Email,

            'SoDienThoai' => $nguoiDung->SoDienThoai,

            'NgaySinh' => $nguoiDung->NgaySinh,

            'VaiTro' => $nguoiDung->VaiTro,
        ]);


        // =====================================================
        // ĐĂNG NHẬP THÀNH CÔNG
        // =====================================================

        return redirect()->route('home');
    }



    public function logout(Request $request)
{
    $request->session()->forget('NguoiDungID');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('home')
        ->with('success', 'Đăng xuất thành công.');
}
}