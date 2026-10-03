<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đăng nhập quản trị - Dollie</title>

    @vite('resources/css/admin/auth.css')
</head>

<body>

    <div class="admin-login-page">

        <div class="admin-login-box">

            {{-- PANEL XANH --}}
            <div class="admin-login-panel">

                {{-- LOGO --}}
                <div class="admin-login-logo">
                    <img src="{{ asset('images/ICONS/logo.png') }}" alt="Dollie">
                </div>

                {{-- TIÊU ĐỀ --}}
                <h1>
                    Đăng nhập quản trị
                </h1>

                <p class="admin-login-subtitle">
                </p>


                {{-- THÔNG BÁO LỖI --}}
                @if($errors->any())
                    <div class="admin-error-message">
                        {{ $errors->first() }}
                    </div>
                @endif


                {{-- THÔNG BÁO THÀNH CÔNG --}}
                @if(session('success'))
                    <div class="admin-success-message">
                        {{ session('success') }}
                    </div>
                @endif


                {{-- FORM ĐĂNG NHẬP --}}
                <form
                    action="{{ route('admin.login.submit') }}"
                    method="POST"
                    class="admin-login-form"
                >

                    @csrf


                    {{-- EMAIL --}}
                    <div class="admin-form-group">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Nhập Email"
                            autocomplete="email"
                            required
                        >

                    </div>


                    {{-- MẬT KHẨU --}}
                    <div class="admin-form-group">

                        <label for="password">
                            Mật khẩu
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Nhập mật khẩu"
                            autocomplete="current-password"
                            required
                        >

                    </div>


                    {{-- NÚT ĐĂNG NHẬP --}}
                    <button
                        type="submit"
                        class="admin-login-button"
                    >
                        ĐĂNG NHẬP
                    </button>

                </form>


                {{-- QUÊN MẬT KHẨU --}}
                <div class="admin-forgot-password">

                    <a href="{{ route('admin.password.request') }}">
                        Quên mật khẩu?
                    </a>

                </div>

            </div>


            {{-- QUAY VỀ TRANG CHỦ --}}
            <div class="admin-login-back">

                <a href="{{ route('home') }}">
                    ← Quay về trang chủ
                </a>

            </div>

        </div>

    </div>

</body>

</html>