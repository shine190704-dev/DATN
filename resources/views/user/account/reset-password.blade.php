@extends('layouts.app')

@section('title', 'Dollie-Đặt lại mật khẩu')

@section('content')

<main class="auth-page forgot-password-page">

    <section class="auth-box" aria-labelledby="reset-password-title">

        <h2 id="reset-password-title">ĐẶT LẠI MẬT KHẨU</h2>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            {{-- Token lấy từ link trong Email --}}
            <input
                type="hidden"
                name="token"
                value="{{ $token }}"
            >

            {{-- Email lấy từ link trong Email --}}
            <input
                type="hidden"
                name="email"
                value="{{ $email }}"
            >


            <!-- MẬT KHẨU MỚI -->
            <div class="auth-field">

                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Mật khẩu mới"
                    required
                >

                @error('password')
                    <div class="auth-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>


            <!-- NHẬP LẠI MẬT KHẨU -->
            <div class="auth-field">

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Nhập lại mật khẩu"
                    required
                >

            </div>


            <!-- NÚT -->
            <div class="forgot-password-actions">

                <a
                    href="{{ route('login') }}"
                    class="auth-button forgot-cancel"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="auth-button"
                >
                    Đặt lại
                </button>

            </div>

        </form>

    </section>

</main>

@endsection