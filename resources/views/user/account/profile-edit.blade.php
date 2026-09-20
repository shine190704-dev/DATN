@extends('layouts.app')

@section('title', 'Chỉnh sửa thông tin')

@section('content')

<main class="account-page">

    @include('partials.account-sidebar')

    {{-- NỘI DUNG CHỈNH SỬA --}}

    <section class="account-content">

        <h1>
            CHỈNH SỬA THÔNG TIN
        </h1>


        {{-- FORM --}}

        <form
            action="{{ route('profile.update') }}"
            method="POST"
            class="account-edit-form"
        >

            @csrf
            @method('PUT')


            <div class="profile-edit-fields">


                {{-- HỌ VÀ TÊN --}}

                <div class="account-info-field">

                    <label for="HoTen">
                        Họ và tên
                    </label>

                    <div class="input-error-row">

                        <input
                            type="text"
                            id="HoTen"
                            name="HoTen"
                            value="{{ old('HoTen', trim(($user->Ho ?? '') . ' ' . ($user->Ten ?? ''))) }}"
                            required
                        >

                        @error('HoTen')

                            <span class="field-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


                {{-- SỐ ĐIỆN THOẠI --}}

                <div class="account-info-field">

                    <label for="SoDienThoai">
                        Số điện thoại
                    </label>

                    <div class="input-error-row">

                        <input
                            type="text"
                            id="SoDienThoai"
                            name="SoDienThoai"
                            value="{{ old('SoDienThoai', $user->SoDienThoai ?? '') }}"
                            required
                        >

                        @error('SoDienThoai')

                            <span class="field-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


                {{-- EMAIL --}}

                <div class="account-info-field">

                    <label for="Email">
                        Email
                    </label>

                    <div class="input-error-row">

                        <input
                            type="email"
                            id="Email"
                            name="Email"
                            value="{{ old('Email', $user->Email ?? '') }}"
                            required
                        >

                        @error('Email')

                            <span class="field-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


                {{-- NGÀY SINH --}}

                <div class="account-info-field">

                    <label for="NgaySinh">
                        Ngày sinh
                    </label>

                    <div class="input-error-row">

                        <input
                            type="date"
                            id="NgaySinh"
                            name="NgaySinh"
                            value="{{ old('NgaySinh', $user->NgaySinh ?? '') }}"
                        >

                        @error('NgaySinh')

                            <span class="field-error">
                                {{ $message }}
                            </span>

                        @enderror

                    </div>

                </div>


            </div>


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
                    Lưu thay đổi
                </button>

            </div>


        </form>


        

    </section>

</main>

@endsection