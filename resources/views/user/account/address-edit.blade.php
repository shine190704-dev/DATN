@extends('layouts.app')

@section('title', isset($address) ? 'Chỉnh sửa địa chỉ' : 'Thêm địa chỉ')

@section('content')

<main class="account-page">

    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}

    <aside class="account-sidebar">

        <div class="account-welcome">

            <img
                src="{{ asset('images/ICONS/profile_icon.png') }}"
                alt="Tài khoản"
            >

            <div class="account-welcome-text">

                <span>
                    Xin chào bạn!
                </span>

                <strong>
                    July
                </strong>

            </div>

        </div>


        <nav class="account-menu">

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('order.*') ? 'active' : '' }}"
            >
                Đơn hàng của tôi
            </a>

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('tracking.*') ? 'active' : '' }}"
            >
                Theo dõi đơn hàng
            </a>

            <a
                href="{{ route('address.index') }}"
                class="account-menu-item {{ request()->routeIs('address.*') ? 'active' : '' }}"
            >
                Sổ địa chỉ
            </a>

            <a
                href="{{ route('profile') }}"
                class="account-menu-item {{ request()->routeIs('profile') ? 'active' : '' }}"
            >
                Thông tin của tôi
            </a>

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('refund.*') ? 'active' : '' }}"
            >
                Yêu cầu hoàn tiền
            </a>

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('review.*') ? 'active' : '' }}"
            >
                Đánh giá của tôi
            </a>


            {{-- ĐĂNG XUẤT --}}
            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf

                <button
                    type="submit"
                    class="account-menu-item"
                >
                    Đăng xuất
                </button>

            </form>

        </nav>

    </aside>


    {{-- =========================================================
         NỘI DUNG
    ========================================================== --}}

    <section class="address-content">

        {{-- =====================================================
             HEADER
        ====================================================== --}}

        <div class="address-header">

            <div>

                <h1>
                    {{ isset($address) ? 'CHỈNH SỬA ĐỊA CHỈ' : 'THÊM ĐỊA CHỈ' }}
                </h1>

                <p>
                    Quản lý địa chỉ nhận hàng của bạn
                </p>

            </div>


            {{-- QUAY LẠI --}}
            <a
                href="{{ route('address.index') }}"
                class="btn-add-address"
            >
                ← Quay lại
            </a>

        </div>


        {{-- =====================================================
             THÔNG BÁO LỖI
        ====================================================== --}}

        @if($errors->any())

            <div class="alert alert-error">

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- =====================================================
             FORM THÊM / CHỈNH SỬA
        ====================================================== --}}
        {{-- =====================================================
     FORM THÊM / CHỈNH SỬA
====================================================== --}}

@if(isset($address))

    <form
        action="{{ route('address.update', $address->DiaChiNguoiDungID) }}"
        method="POST"
        class="address-form-card"
    >

        @csrf
        @method('PUT')

@else

    <form
        action="{{ route('address.store') }}"
        method="POST"
        class="address-form-card"
    >

        @csrf

@endif




            {{-- =================================================
                 TÊN NGƯỜI NHẬN
            ================================================== --}}

            <div class="form-group">

                <label for="TenNguoiNhan">
                    Tên người nhận
                </label>

                <input
                    type="text"
                    id="TenNguoiNhan"
                    name="TenNguoiNhan"
                    value="{{ old('TenNguoiNhan', isset($address) ? $address->TenNguoiNhan : '') }}"
                    placeholder="Nhập tên người nhận"
                    maxlength="200"
                    required
                >

            </div>


            {{-- =================================================
                 SỐ ĐIỆN THOẠI
            ================================================== --}}

            <div class="form-group">

                <label for="SoDienThoai">
                    Số điện thoại
                </label>

                <input
                    type="text"
                    id="SoDienThoai"
                    name="SoDienThoai"
                    value="{{ old('SoDienThoai', isset($address) ? $address->SoDienThoai : '') }}"
                    placeholder="Nhập số điện thoại"
                    maxlength="10"
                    required
                >

            </div>


            {{-- =================================================
                 TỈNH / THÀNH PHỐ
            ================================================== --}}

            <div class="form-group">

                <label for="ThanhPho">
                    Tỉnh / Thành phố
                </label>

                <select
                    id="ThanhPho"
                    name="ThanhPho"
                    required
                >

                    <option value="">
                        -- Chọn tỉnh / thành phố --
                    </option>

                    @foreach($provinces as $province)

                        <option
                            value="{{ $province }}"
                            {{ old(
                                'ThanhPho',
                                isset($address) ? $address->ThanhPho : ''
                            ) == $province ? 'selected' : '' }}
                        >
                            {{ $province }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- =================================================
                 ĐỊA CHỈ CỤ THỂ
            ================================================== --}}

            <div class="form-group">

                <label for="DiaChi">
                    Địa chỉ cụ thể
                </label>

                <textarea
                    id="DiaChi"
                    name="DiaChi"
                    rows="4"
                    maxlength="225"
                    placeholder="Số nhà, tên đường, phường/xã..."
                    required
                >{{ old('DiaChi', isset($address) ? $address->DiaChi : '') }}</textarea>

            </div>


            {{-- =================================================
                 GHI CHÚ
            ================================================== --}}

            @if(!isset($address))

                <div class="form-note">

                    <span>ⓘ</span>

                    Địa chỉ đầu tiên sẽ tự động được đặt làm địa chỉ mặc định.

                </div>

            @endif


            {{-- =================================================
                 BUTTON
            ================================================== --}}

            <div class="address-buttons">

    <div class="address-form-actions">

        <a
            href="{{ route('address.index') }}"
            class="btn-cancel-address"
        >
            Hủy
        </a>

        <button
            type="submit"
            class="btn-save-address"
        >
            Thêm địa chỉ
        </button>

    </div>

</div>


        </form>

    </section>

</main>

@endsection