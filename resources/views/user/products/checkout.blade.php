@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng')

@section('content')


@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

@if($errors->any())
    <script>
        alert("{{ $errors->first() }}");
    </script>
@endif

<main
    class="checkout-page"
    data-subtotal="{{ $subtotal }}"
>

    <div class="checkout-container">

        {{-- =========================================
             TIÊU ĐỀ
        ========================================== --}}

        <h1 class="checkout-title">
            THANH TOÁN ĐƠN HÀNG
        </h1>

        @if($addresses->count() > 0)

            <form
                id="checkoutForm"
                method="POST"
                action="{{ route('checkout.placeOrder') }}"
            >
                @csrf

                {{-- giữ nguyên hidden input cho trường hợp "mua ngay" --}}
                <input type="hidden" name="BienTheID" value="{{ request('BienTheID') }}">
                <input type="hidden" name="SoLuong" value="{{ request('SoLuong', 1) }}">

        @endif


        {{-- =========================================
             ĐỊA CHỈ GIAO HÀNG
        ========================================== --}}

        <section class="checkout-address-section">

            <div class="checkout-section-header">

                <span>
                    Địa chỉ giao hàng
                </span>

                @if($addresses->count() > 0)

                    <a href="{{ route('address.create') }}">
                        Thêm địa chỉ mới
                    </a>

                @endif

            </div>


            {{-- =====================================
                 CÓ ĐỊA CHỈ
            ====================================== --}}

            @if($addresses->count() > 0)

                <div class="checkout-address-list">

                    @foreach($addresses as $address)

                        <label class="checkout-address-item">

                            <input 
                            type="radio" 
                            name="DiaChiNguoiDungID" 
                            value="{{ $address->DiaChiNguoiDungID }}"
                            data-city="{{ $address->ThanhPho }}"
                            {{ $address->MacDinh == 1 ? 'checked' : '' }} 
                        >
                            <div class="checkout-address-content">

                                <div class="checkout-address-name">

                                    <strong>
                                        {{ $address->TenNguoiNhan }}
                                    </strong>

                                    <span>
                                        {{ $address->SoDienThoai }}
                                    </span>

                                    @if($address->MacDinh == 1)

                                        <span class="checkout-default">
                                            Mặc định
                                        </span>

                                    @endif

                                </div>

                                <div class="checkout-address-detail">

                                    {{ $address->DiaChi }},
                                    {{ $address->ThanhPho }}

                                </div>

                            </div>

                        </label>

                    @endforeach

                </div>


            {{-- =====================================
                 CHƯA CÓ ĐỊA CHỈ
            ====================================== --}}

            @else

                <div class="checkout-no-address">

                    <div class="checkout-no-address-title">
                        Bạn chưa có địa chỉ nhận hàng
                    </div>

                    <p>
                        Vui lòng nhập thông tin địa chỉ để tiếp tục đặt hàng.
                    </p>


                    {{-- FORM THÊM ĐỊA CHỈ --}}

                    <form
                        action="{{ route('address.store') }}"
                        method="POST"
                        class="checkout-address-form"
                    >

                        @csrf
                        <input type="hidden" name="return_to" value="checkout">

                        <input
                            type="hidden"
                            name="BienTheID"
                            value="{{ request('BienTheID') }}"
                        >

                        <input
                            type="hidden"
                            name="SoLuong"
                            value="{{ request('SoLuong', 1) }}"
                        >

                        {{-- TÊN NGƯỜI NHẬN --}}

                        <div class="checkout-form-group">

                            <label>
                                Tên người nhận
                            </label>

                            <input
                                type="text"
                                name="TenNguoiNhan"
                                placeholder=""
                                value="{{ old('TenNguoiNhan') }}"
                                maxlength="200"
                                required
                            >

                            @error('TenNguoiNhan')
                                <small class="checkout-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- SỐ ĐIỆN THOẠI --}}

                        <div class="checkout-form-group">

                            <label>
                                Số điện thoại
                            </label>

                            <input
                                type="text"
                                name="SoDienThoai"
                                placeholder=""
                                value="{{ old('SoDienThoai') }}"
                                maxlength="10"
                                required
                            >

                            @error('SoDienThoai')
                                <small class="checkout-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- ĐỊA CHỈ CỤ THỂ --}}

                        <div class="checkout-form-group">

                            <label>
                                Địa chỉ cụ thể
                            </label>

                            <textarea
                                name="DiaChi"
                                rows="3"
                                placeholder="Số nhà, tên đường, phường/xã..."
                                required
                            >{{ old('DiaChi') }}</textarea>

                            @error('DiaChi')
                                <small class="checkout-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- TỈNH / THÀNH PHỐ --}}

                        <div class="checkout-form-group">

                            <label>
                                Thành phố / Tỉnh
                            </label>

                            <select
                                name="ThanhPho"
                                required
                            >

                                <option value="">
                                -- Chọn tỉnh / thành phố --
                                </option>

                                @foreach($provinces as $province)

                                    <option
                                        value="{{ $province }}"
                                        {{ old('ThanhPho') == $province ? 'selected' : '' }}
                                    >
                                        {{ $province }}
                                    </option>

                                @endforeach

                            </select>

                            @error('ThanhPho')
                                <small class="checkout-error">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        {{-- KHÔNG CÓ CHECKBOX MẶC ĐỊNH --}}
                        {{-- Vì địa chỉ đầu tiên sẽ tự động là mặc định --}}

                        <button
                            type="submit"
                            class="checkout-save-address"
                        >
                            Lưu địa chỉ
                        </button>

                    </form>

                </div>

            @endif

        </section>



        {{-- =========================================
     SẢN PHẨM
========================================= --}}

<section class="checkout-products-section">

    <div class="checkout-product-header">

        <div></div>
        <div>Sản phẩm</div>
        <div>Kích thước</div>
        <div>Màu sắc</div>
        <div>Số lượng</div>
        <div>Giá</div>

    </div>


    <div class="checkout-products-list">

        @forelse($items as $item)

            <div class="checkout-product-item">

                {{-- ẢNH --}}
                <div class="checkout-product-image">

                    @if($item->HinhAnh)

                        <img
                            src="{{ asset('images/' . $item->HinhAnh) }}"
                            alt="{{ $item->TenSanPham }}"
                        >

                    @endif

                </div>


                {{-- TÊN --}}
                <div class="checkout-product-name">
                    {{ $item->TenSanPham }}
                </div>


                {{-- KÍCH THƯỚC --}}
                <div class="checkout-product-size">
                    {{ $item->KichThuoc }}
                </div>


                {{-- MÀU --}}
                <div class="checkout-product-color">
                    {{ $item->MauSac }}
                </div>


                {{-- SỐ LƯỢNG --}}
                <div class="checkout-product-quantity">
                    {{ $item->SoLuong }}
                </div>


                {{-- GIÁ --}}
                <div class="checkout-product-price">
                    {{ number_format($item->GiaBienThe, 0, ',', '.') }} VND
                </div>

            </div>

        @empty

            <div class="checkout-no-products">
                Không có sản phẩm trong đơn hàng.
            </div>

        @endforelse

    </div>

</section>
        {{-- =========================================
             MÃ GIẢM GIÁ
        ========================================== --}}

        <section class="checkout-coupon-section">
            <div class="checkout-coupon-row">
                <input
                    type="text"
                    class="checkout-coupon-input"
                    placeholder="Nhập mã khuyến mãi"
                    aria-label="Nhập mã khuyến mãi"
                >

                <button type="button" class="checkout-coupon-btn">
                    Áp dụng
                </button>
            </div>
        </section>

        {{-- =========================================
             TỔNG TIỀN
        ========================================== --}}

        <section class="checkout-total-section">

            <div class="checkout-total-row">

                <span>
                    Tạm tính:
                </span>

                <strong>
                    {{ number_format($subtotal, 0, ',', '.') }} VND
                </strong>

            </div>


            <div class="checkout-total-row">

                <span>
                    Phí vận chuyển:
                </span>

                <strong id="shippingFee">
                    {{ number_format($shippingFee, 0, ',', '.') }} VND
                </strong>

            </div>


            <div class="checkout-total-row checkout-grand-total">

                <span>
                    Tổng cộng:
                </span>

                <strong>
                    {{ number_format($total, 0, ',', '.') }} VND
                </strong>

            </div>

        </section>


        {{-- =========================================
     PHƯƠNG THỨC THANH TOÁN
========================================== --}}

<section class="checkout-payment-section">

    <div class="checkout-placeholder-title">
        Phương thức thanh toán
    </div>

    <div class="checkout-payment-list">

        <label class="checkout-payment-item">

            <input
                type="radio"
                name="PhuongThucThanhToan"
                value="cod"
                checked
            >

            <div class="checkout-payment-content">

                <div class="checkout-payment-name">
                    Thanh toán khi nhận hàng
                </div>

                <div class="checkout-payment-desc">
                    Bạn sẽ thanh toán trực tiếp cho nhân viên giao hàng khi nhận được sản phẩm.
                </div>

            </div>

        </label>


        <label class="checkout-payment-item">

            <input
                type="radio"
                name="PhuongThucThanhToan"
                value="bank_transfer"
            >

            <div class="checkout-payment-content">

                <div class="checkout-payment-name">
                    Chuyển khoản ngân hàng
                </div>

            </div>

        </label>

    </div>

</section>


        @if($addresses->count() > 0)

            <button
                type="submit"
                class="checkout-order-button"
            >
                ĐẶT HÀNG
            </button>

            </form>

        @else

            <button
                type="button"
                class="checkout-order-button"
                disabled
                title="Vui lòng thêm địa chỉ giao hàng trước"
            >
                ĐẶT HÀNG
            </button>

        @endif

    </div>

</main>


@endsection