@extends('layouts.app')

@section('title', 'Tài khoản')

@section('content')

<main class="account-page">

    <aside class="account-sidebar">

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

        <nav class="account-menu">
            <a href="#"
               class="account-menu-item {{ request()->routeIs('order.*') ? 'active' : '' }}">
                Đơn hàng của tôi
            </a>

            <a href="#"
               class="account-menu-item {{ request()->routeIs('tracking.*') ? 'active' : '' }}">
                Theo dõi đơn hàng
            </a>

            <a href="{{ route('address.index') }}"
               class="account-menu-item {{ request()->routeIs('address.*') ? 'active' : '' }}">
                Sổ địa chỉ
            </a>

            <a href="{{ route('profile') }}"
               class="account-menu-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                Thông tin của tôi
            </a>

            <a href="#"
               class="account-menu-item {{ request()->routeIs('refund.*') ? 'active' : '' }}">
                Yêu cầu hoàn tiền
            </a>

            <a href="#"
               class="account-menu-item {{ request()->routeIs('review.*') ? 'active' : '' }}">
                Đánh giá của tôi
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="account-menu-item">Đăng xuất</button>
            </form>
        </nav>

    </aside>

  {{-- NỘI DUNG THÔNG TIN CÁ NHÂN --}}
<section class="account-content">

    <h1>
        THÔNG TIN CỦA TÔI
    </h1>

    {{-- THÔNG BÁO --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- THÔNG TIN --}}
    <div class="account-info-grid">

        {{-- HỌ VÀ TÊN --}}
        <div class="account-info-field">

            <span>
                Họ và tên
            </span>

            <strong>
                {{ trim(($user->Ho ?? '') . ' ' . ($user->Ten ?? '')) }}
            </strong>

        </div>


        {{-- SỐ ĐIỆN THOẠI --}}
        <div class="account-info-field">

            <span>
                Số điện thoại
            </span>

            <strong>
                {{ $user->SoDienThoai ?? 'Chưa cập nhật' }}
            </strong>

        </div>


        {{-- NGÀY SINH --}}
        <div class="account-info-field">

            <span>
                Ngày sinh
            </span>

            <strong>

                @if(isset($user->NgaySinh) && $user->NgaySinh)

                    {{ \Carbon\Carbon::parse($user->NgaySinh)->format('d/m/Y') }}

                @else

                    Chưa cập nhật

                @endif

            </strong>

        </div>


        {{-- EMAIL --}}
        <div class="account-info-field">

            <span>
                Email
            </span>

            <strong>
                {{ $user->Email ?? 'Chưa cập nhật' }}
            </strong>

        </div>

    </div>


    {{-- NÚT CHỈNH SỬA --}}
    <div class="account-edit">

        <a
            href="{{ route('profile.edit') }}"
            class="account-edit-button"
        >
            Chỉnh sửa
        </a>

    </div>

</section>

</main>


@endsection




