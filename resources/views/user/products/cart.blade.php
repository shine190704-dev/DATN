@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')

<main
    class="cart-page"
    data-update-url="{{ route('cart.update') }}"
    data-remove-url="{{ route('cart.remove') }}"
    data-csrf-token="{{ csrf_token() }}"
>

    <h1 class="cart-title" data-cart-title>
        Giỏ hàng của bạn ({{ $items->sum('SoLuong') }})
    </h1>

    @if($items->count() > 0)

        <div class="cart-table">

            {{-- HEADER --}}
            <div class="cart-header">

                <div class="cart-column cart-product-column">
                    Sản phẩm
                </div>

                <div class="cart-column">
                    Kích thước
                </div>

                <div class="cart-column">
                    Màu sắc
                </div>

                <div class="cart-column">
                    Số lượng
                </div>

                <div class="cart-column">
                    Giá
                </div>

                <div class="cart-column">
                    Tổng
                </div>

                <div class="cart-column cart-remove-column">
                </div>

            </div>


            {{-- DANH SÁCH SẢN PHẨM --}}
            @foreach($items as $item)

                <div
                    class="cart-item"
                    data-cart-item-id="{{ $item->ChiTietGioHangID }}"
                    data-unit-price="{{ $item->GiaBienThe }}"
                >

                    {{-- SẢN PHẨM --}}
                    <div class="cart-product">

                        <div class="cart-product-image">

                            @if($item->HinhAnh)

                                <img
                                    src="{{ asset('images/' . $item->HinhAnh) }}"
                                    alt="{{ $item->TenSanPham }}"
                                >

                            @endif

                        </div>

                        <div class="cart-product-name">
                            {{ $item->TenSanPham }}
                        </div>

                    </div>


                    {{-- KÍCH THƯỚC --}}
                    <div class="cart-item-size">
                        {{ $item->KichThuoc }}
                    </div>


                    {{-- MÀU SẮC --}}
                    <div class="cart-item-color">
                        {{ $item->MauSac }}
                    </div>


                    {{-- SỐ LƯỢNG --}}
                    <div class="cart-item-quantity">

                        <button
                            type="button"
                            class="cart-quantity-btn cart-quantity-decrease"
                            data-action="decrease"
                            data-item-id="{{ $item->ChiTietGioHangID }}"
                        >
                            −
                        </button>

                        <span class="cart-quantity">
                            {{ $item->SoLuong }}
                        </span>

                        <button
                            type="button"
                            class="cart-quantity-btn cart-quantity-increase"
                            data-action="increase"
                            data-item-id="{{ $item->ChiTietGioHangID }}"
                        >
                            +
                        </button>

                    </div>


                    {{-- GIÁ --}}
                    <div class="cart-item-price">

                        {{ number_format($item->GiaBienThe, 0, ',', '.') }}
                        VND

                    </div>


                    {{-- THÀNH TIỀN --}}
                    <div class="cart-item-total" data-item-total>

                        {{ number_format($item->ThanhTien, 0, ',', '.') }}
                        VND

                    </div>


                    {{-- XÓA --}}
                    <div class="cart-item-remove">

                        <button
                            type="button"
                            class="cart-remove-btn"
                            data-item-id="{{ $item->ChiTietGioHangID }}"
                            aria-label="Xóa {{ $item->TenSanPham }}"
                        >
                            ×
                        </button>

                    </div>

                </div>

            @endforeach

        </div>


        {{-- TỔNG TIỀN --}}
        <div class="cart-summary">

            <span>
                Tổng cộng:
            </span>

            <strong id="cartTotal">

                {{ number_format($total, 0, ',', '.') }}
                VND

            </strong>

        </div>


        {{-- THANH TOÁN --}}
        <div class="cart-checkout">

            <button
                type="button"
                class="cart-checkout-btn"
            >
                THANH TOÁN
            </button>

        </div>


    @else

        {{-- GIỎ HÀNG TRỐNG --}}
        <div class="cart-empty">

            <p>
                Giỏ hàng của bạn đang trống.
            </p>

            <a
                href="{{ route('product.all') }}"
                class="cart-empty-btn"
            >
                TIẾP TỤC MUA SẮM
            </a>

        </div>

    @endif

</main>

@endsection