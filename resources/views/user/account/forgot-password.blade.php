@extends('layouts.app')

@section('title', 'Dollie-Quên mật khẩu')

@section('content')

<main class="auth-page forgot-password-page">

    <section class="auth-box" aria-labelledby="forgot-password-title">

        <h2 id="forgot-password-title">ĐĂNG NHẬP</h2>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="auth-field">
                <label for="email">Phục hồi mật khẩu</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    value="{{ old('email') }}"
                    placeholder="Email"
                    required
                >

                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            @if (session('success'))
                <div class="auth-success">{{ session('success') }}</div>
            @endif

            <div class="forgot-password-actions">
                <a href="{{ route('login') }}" class="auth-button forgot-cancel">Hủy</a>

                <button type="submit" class="auth-button">Gửi</button>
            </div>

        </form>

    </section>

</main>

@endsection