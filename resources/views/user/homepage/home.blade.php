@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')

<main class="home-page">

    <!-- BANNER 1 -->
    <section class="home-banner">

        <img
            src="{{ asset('images/Banner.png') }}"
            alt="Banner"
        >

    </section>


    <!-- =========================================
         SẢN PHẨM NỔI BẬT
         4 sản phẩm bán nhiều nhất
    ========================================== -->

    <section class="best-selling">

        <h2>SẢN PHẨM NỔI BẬT</h2>

        @include('user.products.product-list', [
            'products' => $sanPhamNoiBat
        ])

    </section>


    

    <!-- BANNER 2 -->
    <section class="banner-two">

    <img
        src="{{ asset('images/Banner2.png') }}"
        alt="Banner"
    >

    <div class="banner-two-content">

        <div class="banner-two-text">
            Cảm ơn bạn đã đến với chúng tôi.<br>
            Hãy cùng khám phá những bộ sưu<br>
            tập gấu bông mới đầy đáng yêu.
        </div>
        <a href="{{ route('product.new') }}" class="banner-two-button">
            Khám phá thêm
            <span>→</span>
        </a>

    </div>

        <div class="banner-two-right-image">
            <img
            src="{{ asset('images/image_banner2.png') }}"
            alt="Gấu bông"
        >
</div>
    

</section>


    <!-- =========================================
         DÀNH CHO BẠN
    ========================================== -->

    <section class="best-selling recommended-products">

        <h2>DÀNH CHO BẠN</h2>

        @include('user.products.product-list', [
            'products' => $sanPhamDanhChoBan
        ])

    </section>


</main>

@endsection