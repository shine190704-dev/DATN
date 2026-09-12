@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')

<main class="auth-page register-page">
    <section class="auth-box" aria-labelledby="register-title">

        <h2 id="register-title">ĐĂNG KÝ</h2>

        @if (session('success'))
            <div class="auth-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

            <div class="auth-field">
                <label for="name">Họ và tên</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    value="{{ old('name') }}"
                    required
                >
                @error('name') <div class="auth-error">{{ $message }}</div> @enderror
            </div>

            <div class="auth-field">
                <label for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    value="{{ old('email') }}"
                    required
                >
                @error('email') <div class="auth-error">{{ $message }}</div> @enderror
            </div>

            <div class="auth-field">
                <label for="phone">Số điện thoại</label>
                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    autocomplete="tel"
                    maxlength="10"
                    inputmode="numeric"
                    value="{{ old('phone') }}"
                    required
                >
                @error('phone') <div class="auth-error">{{ $message }}</div> @enderror
            </div>

            <div class="auth-field">
                <label for="birthday">Ngày sinh</label>
                <input
                    id="birthday"
                    name="birthday"
                    type="date"
                    value="{{ old('birthday') }}"
                    required
                >
                @error('birthday') <div class="auth-error">{{ $message }}</div> @enderror
            </div>

            <div class="auth-field">
                <label for="password">Mật khẩu</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                >
                @error('password') <div class="auth-error">{{ $message }}</div> @enderror
            </div>

            <button class="auth-button" type="submit">
                ĐĂNG KÝ
            </button>

        </form>

        <div class="auth-links">
            <span>Đã có tài khoản?</span>
            <a href="{{ route('login') }}">Đăng nhập</a>
        </div>

    </section>
</main>

@endsection