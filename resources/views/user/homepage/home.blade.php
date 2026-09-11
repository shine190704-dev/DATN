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
            alt="Banner 2"
        >

    </section>


    <!-- =========================================
         DÀNH CHO BẠN
         4 sản phẩm bán ít hơn
    ========================================== -->

    <section class="best-selling recommended-products">

        <h2>DÀNH CHO BẠN</h2>

        @include('user.products.product-list', [
            'products' => $sanPhamDanhChoBan
        ])

    </section>


</main>

@endsection