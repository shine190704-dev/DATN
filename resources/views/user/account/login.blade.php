@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')

<main class="auth-page login-page">
    <section class="auth-box" aria-labelledby="login-title">

        <h2 id="login-title">ĐĂNG NHẬP</h2>

        @if (session('success'))
            <div class="auth-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

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

                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="auth-field">
                <label for="password">Mật khẩu</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                >

                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <button class="auth-button" type="submit">
                ĐĂNG NHẬP
            </button>
        </form>

        <div class="auth-links">
            <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
            <span>|</span>
            <a href="{{ route('register') }}">Đăng ký ngay</a>
        </div>

    </section>
</main>

@endsection