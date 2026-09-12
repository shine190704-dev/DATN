@extends('layouts.app')

@section('title', $danhMuc->TenDanhMuc)

@section('content')

<main class="product-page">

    {{-- SẢN PHẨM THEO DANH MỤC --}}
    <section class="all-products">

    @include('user.products.product-sort')

    @include('user.products.product-list', [
        'products' => $products
    ])

</section>

</main>

@endsection