<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Quên mật khẩu - Dollie</title>

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
                    Quên mật khẩu
                </h1>

                {{-- Thông báo lỗi --}}
                @if($errors->any())
                    <div class="admin-error-message">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Thông báo thành công --}}
                @if(session('success'))
                    <div class="admin-success-message">
                        {{ session('success') }}
                    </div>
                @endif

                <form
                    action="{{ route('admin.password.email') }}"
                    method="POST"
                    class="admin-login-form"
                >
                    @csrf

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

                    <button
                        type="submit"
                        class="admin-login-button"
                    >
                        GỬI LINK ĐẶT LẠI
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