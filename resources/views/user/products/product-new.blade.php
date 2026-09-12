@extends('layouts.app')

@section('title', 'Sản phẩm mới')

@section('content')

<main class="product-page">

    <section class="all-products">

    {{-- SẮP XẾP --}}
    @include('user.products.product-sort')

    {{-- DANH SÁCH SẢN PHẨM --}}
    @include('user.products.product-list', [
        'products' => $products
    ])

</section>

</main>

@endsection