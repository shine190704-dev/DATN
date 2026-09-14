@extends('layouts.app')

@section('title', $product->TenSanPham)

@section('content')

<main
    class="product-detail-page"
    data-variants='@json($variants)'
>

    <section class="product-detail-gallery">
        <div class="product-main-image-wrap">
            <button type="button" class="product-gallery-arrow product-gallery-prev" aria-label="Ảnh trước">
                ‹
            </button>

            <div class="product-main-image">
            @if($images->count() > 0)
                <img
                    id="mainProductImage"
                    src="{{ asset('images/' . $images->first()->DuongDanAnh) }}"
                    alt="{{ $product->TenSanPham }}"
                >
            @endif
            </div>

            <button type="button" class="product-gallery-arrow product-gallery-next" aria-label="Ảnh tiếp theo">
                ›
            </button>
        </div>

        <div class="product-thumbnails" aria-label="Các ảnh sản phẩm">
            @foreach($images as $image)
                <button
                    type="button"
                    class="product-thumbnail {{ $loop->first ? 'active' : '' }}"
                    data-image-url="{{ asset('images/' . $image->DuongDanAnh) }}"
                    data-image-index="{{ $loop->index }}"
                    aria-label="Chọn ảnh {{ $loop->iteration }}"
                >
                    <img
                        src="{{ asset('images/' . $image->DuongDanAnh) }}"
                        alt="{{ $product->TenSanPham }} - ảnh {{ $loop->iteration }}"
                    >
                </button>
            @endforeach
        </div>
    </section>

    <section class="product-detail-info">
        <div class="product-detail-heading">
            <h1 class="product-detail-name">{{ $product->TenSanPham }}</h1>
            <button
                type="button"
                class="product-detail-favorite {{ $isFavorite ? 'active' : '' }}"
                data-product-id="{{ $product->SanPhamID }}"
                data-wishlist-url="{{ route('wishlist.toggle') }}"
                data-csrf-token="{{ csrf_token() }}"
                data-remove-on-unfavorite="false"
                aria-pressed="{{ $isFavorite ? 'true' : 'false' }}"
            >
                {{ $isFavorite ? 'Đã thêm vào yêu thích' : 'Thêm vào yêu thích' }}
            </button>
        </div>

        <div class="product-detail-card">
            <span>Kích thước</span>
            <div class="product-detail-options">
                @forelse($variants->pluck('KichThuoc')->unique() as $size)
                    <button
                        type="button"
                        class="variant-size {{ $loop->first ? 'active' : '' }}"
                        data-size="{{ $size }}"
                    >
                        {{ $size }}
                    </button>
                @empty
                    <span>Chưa cập nhật</span>
                @endforelse
            </div>
        </div>

        <div class="product-detail-card">
            <span>Màu sắc</span>
            <div class="product-detail-options">
                @forelse($variants->pluck('MauSac')->unique() as $color)
                    <button
                        type="button"
                        class="variant-color {{ $loop->first ? 'active' : '' }}"
                        data-color="{{ $color }}"
                    >
                        {{ $color }}
                    </button>
                @empty
                    <span>Chưa cập nhật</span>
                @endforelse
            </div>
        </div>

        <div class="product-detail-card">
            <span>Số lượng</span>
            <div class="quantity-control">
                <button type="button" class="quantity-decrease">−</button>
                <span id="selectedQuantity">1</span>
                <button type="button" class="quantity-increase">+</button>
            </div>
        </div>

        <div class="product-detail-card product-detail-price-row">
            <span>Giá</span>
            <strong id="variantPrice">
                {{ number_format($variants->first()->GiaBienThe ?? $product->Gia, 0, ',', '.') }} VND
            </strong>
        </div>

        <div class="product-detail-actions">
            <button
                type="button"
                class="product-add-cart"
                data-cart-url="{{ route('cart.add') }}"
                data-csrf-token="{{ csrf_token() }}"
            >
                Thêm vào giỏ
            </button>
            <button type="button" class="product-buy-now">Mua ngay</button>
        </div>

        <div class="product-detail-description">
            <h2>Mô tả</h2>
            <p>{{ $product->MoTa ?: 'Được làm từ chất liệu vải mềm mại cùng lớp bông êm ái, mang đến cảm giác dễ chịu và thoải mái mỗi khi ôm.' }}</p>
        </div>

    </section>

</main>

@endsection