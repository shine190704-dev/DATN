<div class="product-list">

    @php
        $favoriteProductIds = $favoriteProductIds ?? collect();
        $removeOnUnfavorite = $removeOnUnfavorite ?? false;
    @endphp

    @foreach($products as $product)

        <div class="product-card">

            <!-- ẢNH -->
            <div class="product-image">

                @if($product->HinhAnh)

                    <a
                        href="{{ route('product.detail', $product->SanPhamID) }}"
                        class="product-image-link"
                        aria-label="Xem chi tiết {{ $product->TenSanPham }}"
                    >
                        <img
                            src="{{ asset('images/' . $product->HinhAnh) }}"
                            alt="{{ $product->TenSanPham }}"
                        >
                    </a>

                @endif

            </div>


            <!-- TÊN -->
            <div class="product-name">

                {{ $product->TenSanPham }}

            </div>


            <!-- THÔNG TIN -->
            <div class="product-info">

                <!-- ĐÁNH GIÁ -->
                <div class="product-rating">

                    <img
                        src="{{ asset('images/ICONS/star_icon.png') }}"
                        alt="Đánh giá"
                    >

                    <span>4.8</span>

                </div>


                <!-- GIÁ -->
                <div class="product-price">

                    {{ number_format($product->Gia, 0, ',', '.') }} VND

                </div>


                <!-- YÊU THÍCH -->
        
                <button
                    type="button"
                    data-product-id="{{ $product->SanPhamID }}"
                    data-product-name="{{ $product->TenSanPham }}"
                    data-wishlist-url="{{ route('wishlist.toggle') }}"
                    data-csrf-token="{{ csrf_token() }}"
                    data-remove-on-unfavorite="{{ $removeOnUnfavorite ? 'true' : 'false' }}"
                    class="product-favorite {{ $favoriteProductIds->contains($product->SanPhamID) ? 'active' : '' }}"
                    aria-pressed="{{ $favoriteProductIds->contains($product->SanPhamID) ? 'true' : 'false' }}"
                    title="{{ $favoriteProductIds->contains($product->SanPhamID) ? 'Bỏ khỏi yêu thích' : 'Thêm vào yêu thích' }}"
                >
                    <i class="{{ $favoriteProductIds->contains($product->SanPhamID) ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                </button>

            </div>

        </div>

    @endforeach

</div>