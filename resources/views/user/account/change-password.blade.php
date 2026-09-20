@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')

<main class="account-page">

    @include('partials.account-sidebar')

    {{-- NỘI DUNG ĐỔI MẬT KHẨU --}}
    <section class="account-content">

        <h1>
            ĐỔI MẬT KHẨU
        </h1>


        {{-- THÔNG BÁO --}}
        @if(session('success'))

            <div class="alert alert-success">
                {{ session('success') }}
            </div>

        @endif


        {{-- FORM --}}
        <form
            action="{{ route('account.password.update') }}"
            method="POST"
            class="account-edit-form"
            novalidate
        >

            @csrf


            <div class="profile-edit-fields">


                {{-- MẬT KHẨU HIỆN TẠI --}}
                <div class="account-info-field">

                    <label for="current_password">
                        Mật khẩu hiện tại
                    </label>

                    <div class="input-error-row">

                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            required
                        >

                        @error('current_password')

                            <span class="field-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


                {{-- MẬT KHẨU MỚI --}}
                <div class="account-info-field">

                    <label for="new_password">
                        Mật khẩu mới
                    </label>

                    <div class="input-error-row">

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            autocomplete="new-password"
                            required
                        >

                        @error('new_password')

                            <span class="field-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


                {{-- NHẬP LẠI MẬT KHẨU MỚI --}}
                <div class="account-info-field">

                    <label for="new_password_confirmation">
                        Nhập lại mật khẩu mới
                    </label>

                    <div class="input-error-row">

                        <input
                            type="password"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                </div>


            </div>


            {{-- HIỆN MẬT KHẨU --}}
            <label style="display:inline-block; margin-top:14px; color:#59A78E; font-size:13px; cursor:pointer;">
                <input type="checkbox" id="showPasswords">
                Hiện mật khẩu
            </label>


            {{-- NÚT HỦY / LƯU --}}
            <div class="account-edit">

                <a
                    href="{{ route('profile') }}"
                    class="account-edit-button"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="account-edit-button"
                >
                    Cập nhật mật khẩu
                </button>

            </div>

        </form>

    </section>

</main>



@endsection