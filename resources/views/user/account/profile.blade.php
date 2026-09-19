@extends('layouts.app')

@section('title', 'Tài khoản')

@section('content')

<main class="account-page">

    @include('partials.account-sidebar')

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




