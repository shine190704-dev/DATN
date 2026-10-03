<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Đặt lại mật khẩu - Dollie</title>

    @vite('resources/css/admin/auth.css')
</head>

<body>

    <div class="admin-login-page">

        <div class="admin-login-box">

            <div class="admin-login-panel">

                <div class="admin-login-logo">
                    <img src="{{ asset('images/ICONS/logo.png') }}" alt="Dollie">
                </div>

                <h1>
                    Đặt lại mật khẩu
                </h1>

                <p class="admin-login-subtitle">

                </p>

                @if($errors->any())
                    <div class="admin-error-message">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form
                    action="{{ route('admin.password.update') }}"
                    method="POST"
                    class="admin-login-form"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="token"
                        value="{{ $token }}"
                    >

                    <input
                        type="hidden"
                        name="email"
                        value="{{ $email }}"
                    >

                    <div class="admin-form-group">

                        <label for="password">
                            Mật khẩu mới
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Nhập mật khẩu mới"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                    <div class="admin-form-group">

                        <label for="password_confirmation">
                            Nhập lại mật khẩu
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Nhập lại mật khẩu"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="admin-login-button"
                    >
                        ĐẶT LẠI MẬT KHẨU
                    </button>

                </form>

            </div>

            <div class="admin-login-back">
                <a href="{{ route('admin.login') }}">
                    ← Quay lại đăng nhập
                </a>
            </div>

        </div>

    </div>

</body>

</html>