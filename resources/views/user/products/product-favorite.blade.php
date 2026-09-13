@extends('layouts.app')

@section('title', 'Sản phẩm yêu thích')

@section('content')

<main class="product-page">
    <section class="all-products">

        <h2>SẢN PHẨM YÊU THÍCH</h2>

        @if($products->count() > 0)

            @include('user.products.product-list', [
                'products' => $products,
                'removeOnUnfavorite' => true
            ])

        @else

            <div class="wishlist-empty">
                <p>Chưa có sản phẩm yêu thích.</p>
            </div>

        @endif

    </section>

</main>

@endsection