<div class="product-list">

    @foreach($products as $product)

        <div class="product-card">

            <!-- ẢNH -->
            <div class="product-image">

                @if($product->HinhAnh)

                    <img
                        src="{{ asset('images/' . $product->HinhAnh) }}"
                        alt="{{ $product->TenSanPham }}"
                    >

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
                    class="product-favorite"
                    data-product-id="{{ $product->SanPhamID }}"
                    data-product-name="{{ $product->TenSanPham }}"
                    aria-pressed="false"
                    title="Thêm vào yêu thích"
                >
                    <img
                        src="{{ asset('images/ICONS/product_heart.png') }}"
                        alt="Yêu thích"
                    >
                </button>

            </div>

        </div>

    @endforeach

</div>