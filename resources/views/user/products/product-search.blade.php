@extends('layouts.app')

@section('title', 'Tìm kiếm sản phẩm')

@section('content')

<main class="product-page search-results-page">


    <section class="all-products">

        @if($keyword !== '')

            <h2>
                KẾT QUẢ TÌM KIẾM: "{{ $keyword }}"
            </h2>

            @if($products->count() > 0)

                @include('user.products.product-list', [
                    'products' => $products
                ])

            @else

                <div class="search-empty">
                    Không tìm thấy sản phẩm phù hợp.
                </div>

            @endif

        @else

            <h2>TÌM KIẾM SẢN PHẨM</h2>

            <div class="search-empty">
                Vui lòng nhập từ khóa để tìm kiếm sản phẩm.
            </div>

        @endif

    </section>

</main>

@endsection


