@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')

<main class="auth-page register-page">

    <div class="auth-box">

        <h2>ĐĂNG KÝ</h2>

        <form>

            <div class="auth-field">
                <label for="name">Họ và tên</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    required
                >
            </div>

            <div class="auth-field">
                <label for="phone">Số điện thoại</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    required
                >
            </div>

            <div class="auth-field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                >
            </div>

            <div class="auth-field">
                <label for="birthday">Ngày sinh</label>
                <input
                    type="date"
                    id="birthday"
                    name="birthday"
                    required
                >
            </div>

            <button type="submit" class="auth-button">
                ĐĂNG KÝ
            </button>

        </form>

        <div class="auth-links">
            <span>Bạn đã có tài khoản?</span>
            <a href="{{ route('login') }}">
                Đăng nhập tại đây
            </a>
        </div>

    </div>

</main>

@endsection