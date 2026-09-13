@extends('layouts.app')

@section('title', 'Tài khoản')

@section('content')

<main class="account-page">

    <aside class="account-sidebar">

        <!-- LỜI CHÀO -->
        <div class="account-welcome">

            <img
                src="{{ asset('images/ICONS/profile_icon.png') }}"
                alt="Tài khoản"
            >

            <div class="account-welcome-text">
                <span>Xin chào bạn!</span>

                <strong>July</strong>
            </div>

        </div>


        <!-- MENU -->
        <nav class="account-menu">

            <a href="#" class="account-menu-item">
                Đơn hàng của tôi
            </a>

            <a href="#" class="account-menu-item">
                Theo dõi đơn hàng
            </a>

            <a href="#" class="account-menu-item">
                Sổ địa chỉ
            </a>

            <a href="#" class="account-menu-item active">
                Thông tin của tôi
            </a>

            <a href="#" class="account-menu-item">
                Yêu cầu hoàn tiền
            </a>

            <a href="#" class="account-menu-item">
                Đánh giá của tôi
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="account-menu-item">
                    Đăng xuất
                </button>
            </form>

        </nav>

    </aside>

    <section class="account-content"></section>

</main>

@endsection