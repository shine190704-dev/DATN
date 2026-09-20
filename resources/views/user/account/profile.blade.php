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



    {{-- ĐỔI MẬT KHẨU --}}
<details
    class="password-section"
    @if($errors->has('current_password') || $errors->has('new_password')) open @endif
>

    <summary>Đổi mật khẩu</summary>

    <form
        action="{{ route('account.password.update') }}"
        method="POST"
        class="account-edit-form"
        novalidate
    >
        @csrf

        <div class="profile-edit-fields">

            <div class="account-info-field">
                <label for="current_password">Mật khẩu hiện tại</label>
                <div class="input-error-row">
                    <input type="password" id="current_password" name="current_password"
                           autocomplete="current-password" required>
                    @error('current_password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="account-info-field">
                <label for="new_password">Mật khẩu mới</label>
                <div class="input-error-row">
                    <input type="password" id="new_password" name="new_password"
                           autocomplete="new-password" required>
                    @error('new_password')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="account-info-field">
                <label for="new_password_confirmation">Nhập lại mật khẩu mới</label>
                <div class="input-error-row">
                    <input type="password" id="new_password_confirmation"
                           name="new_password_confirmation"
                           autocomplete="new-password" required>
                </div>
            </div>

        </div>

        <label style="display:inline-block; margin-top:14px; color:#59A78E; font-size:13px; cursor:pointer;">
            <input type="checkbox" id="showPasswords">
            Hiện mật khẩu
        </label>

        <div class="account-edit">
            <button type="submit" class="account-edit-button">
                Cập nhật mật khẩu
            </button>
        </div>

    </form>

</details>

</section>

</main>


@endsection




