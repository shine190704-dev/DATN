<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    /**
     * Trang đăng nhập Admin / Nhân viên
     */
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    /**
     * Xử lý đăng nhập Admin / Nhân viên
     */
    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                ],

                'password' => [
                    'required',
                    'string',
                ],
            ],
            [
                'email.required' => 'Vui lòng nhập Email.',
                'email.email' => 'Email không đúng định dạng.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
            ]
        );

        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->first();

        // Không tìm thấy tài khoản
        if (!$nguoiDung) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Tài khoản không tồn tại.',
                ]);
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($request->password, $nguoiDung->MatKhau)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Mật khẩu không chính xác.',
                ]);
        }

        // Chỉ Admin và Nhân viên được vào khu vực quản trị
        if (!in_array($nguoiDung->VaiTro, ['Admin', 'NhanVien'])) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Tài khoản không có quyền truy cập khu vực quản trị.',
                ]);
        }

        // Kiểm tra trạng thái
        if ($nguoiDung->TrangThai !== 'HoatDong') {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Tài khoản của bạn hiện không hoạt động.',
                ]);
        }

        // Chống Session Fixation
        $request->session()->regenerate();

        // Lưu session Admin
        session([
            'AdminNguoiDungID' => $nguoiDung->NguoiDungID,
            'AdminHo' => $nguoiDung->Ho,
            'AdminTen' => $nguoiDung->Ten,
            'AdminEmail' => $nguoiDung->Email,
            'AdminVaiTro' => $nguoiDung->VaiTro,
        ]);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Đăng nhập quản trị thành công.');
    }


    /**
     * =========================================================
     * TRANG QUÊN MẬT KHẨU
     * =========================================================
     */
    public function showForgotPassword()
    {
        return view('admin.auth.forgot-password');
    }


    /**
     * =========================================================
     * GỬI LINK ĐẶT LẠI MẬT KHẨU
     * =========================================================
     */
    public function sendResetLink(Request $request)
    {
        // Kiểm tra Email
        $request->validate(
            [
                'email' => [
                    'required',
                    'email:rfc,dns',
                ],
            ],
            [
                'email.required' => 'Vui lòng nhập Email.',
                'email.email' => 'Email không đúng định dạng.',
            ]
        );

        // Tìm tài khoản
        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->first();

        // Email không tồn tại
        if (!$nguoiDung) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Tài khoản không tồn tại.',
                ]);
        }

        // Nhân viên không được reset qua email
        if ($nguoiDung->VaiTro === 'NhanVien') {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Tài khoản nhân viên không hỗ trợ đặt lại mật khẩu qua email.',
                ]);
        }

        // Nếu không phải Admin
        if ($nguoiDung->VaiTro !== 'Admin') {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Tài khoản không có quyền sử dụng chức năng này.',
                ]);
        }

        // Tài khoản không hoạt động
        if ($nguoiDung->TrangThai !== 'HoatDong') {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Tài khoản của bạn hiện không hoạt động.',
                ]);
        }

        // =====================================================
        // TẠO TOKEN
        // =====================================================

        $token = Str::random(64);

        // Hash token trước khi lưu DB
        $tokenHash = Hash::make($token);

        // Token có hiệu lực 15 phút
        $thoiGianHetHan = now()->addMinutes(15);

        // Lưu token vào tài khoản Admin
        DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDung->NguoiDungID)
            ->update([
                'MaDatLaiMatKhau' => $tokenHash,
                'ThoiGianHetHanMaDatLaiMatKhau' => $thoiGianHetHan,
            ]);

        // =====================================================
        // TẠO LINK RESET
        // =====================================================

        $resetLink = route('admin.password.reset', [
            'token' => $token,
            'email' => $nguoiDung->Email,
        ]);

        // =====================================================
        // GỬI EMAIL
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
                    Đặt lại mật khẩu quản trị
                </h3>

                <p>
                    Xin chào ' . e($nguoiDung->Ho . ' ' . $nguoiDung->Ten) . ',
                </p>

                <p>
                    Bạn vừa yêu cầu đặt lại mật khẩu cho tài khoản quản trị Dollie.
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
                            color: #2F6F5B;
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
                    ->subject('Dollie - Đặt lại mật khẩu quản trị');
            }
        );

        return back()->with(
            'success',
            'Liên kết đặt lại mật khẩu đã được gửi đến Email của bạn.'
        );
    }


    /**
     * =========================================================
     * TRANG ĐẶT LẠI MẬT KHẨU
     * =========================================================
     */
    public function showResetPassword(Request $request, $token)
    {
        $email = $request->query('email');

        // Tìm Admin còn token và chưa hết hạn
        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $email)
            ->where('VaiTro', 'Admin')
            ->whereNotNull('MaDatLaiMatKhau')
            ->where(
                'ThoiGianHetHanMaDatLaiMatKhau',
                '>=',
                now()
            )
            ->first();

        // Token không hợp lệ
        if (
            !$nguoiDung ||
            !Hash::check(
                $token,
                $nguoiDung->MaDatLaiMatKhau
            )
        ) {
            return redirect()
                ->route('admin.password.request')
                ->withErrors([
                    'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
                ]);
        }

        return view(
            'admin.auth.reset-password',
            [
                'token' => $token,
                'email' => $email,
            ]
        );
    }


    /**
     * =========================================================
     * XỬ LÝ ĐẶT LẠI MẬT KHẨU
     * =========================================================
     */
    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'token' => [
                    'required',
                ],

                'email' => [
                    'required',
                    'email',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'confirmed',
                ],
            ],
            [
                'email.required' => 'Email không hợp lệ.',
                'email.email' => 'Email không đúng định dạng.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
                'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
            ]
        );

        // Tìm Admin còn token
        $nguoiDung = DB::table('NguoiDung')
            ->where('Email', $request->email)
            ->where('VaiTro', 'Admin')
            ->whereNotNull('MaDatLaiMatKhau')
            ->where(
                'ThoiGianHetHanMaDatLaiMatKhau',
                '>=',
                now()
            )
            ->first();

        // Kiểm tra token
        if (
            !$nguoiDung ||
            !Hash::check(
                $request->token,
                $nguoiDung->MaDatLaiMatKhau
            )
        ) {
            return redirect()
                ->route('admin.password.request')
                ->withErrors([
                    'email' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.',
                ]);
        }

        // Cập nhật mật khẩu
        DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDung->NguoiDungID)
            ->update([
                'MatKhau' => Hash::make($request->password),

                // Token chỉ được dùng một lần
                'MaDatLaiMatKhau' => null,
                'ThoiGianHetHanMaDatLaiMatKhau' => null,

                'NgayCapNhat' => now(),
            ]);

        return redirect()
            ->route('admin.login')
            ->with(
                'success',
                'Đặt lại mật khẩu thành công. Vui lòng đăng nhập.'
            );
    }


    /**
     * =========================================================
     * ĐĂNG XUẤT
     * =========================================================
     */
    public function logout(Request $request)
    {
        $request->session()->forget([
            'AdminNguoiDungID',
            'AdminHo',
            'AdminTen',
            'AdminEmail',
            'AdminVaiTro',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('success', 'Đăng xuất thành công.');
    }
}