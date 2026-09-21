@extends('layouts.app')

@section('title', 'Đánh giá của tôi')

@section('content')

<main class="account-page reviews-page">

    {{-- =====================================================
         SIDEBAR
    ====================================================== --}}
    @include('partials.account-sidebar')


    {{-- =====================================================
         NỘI DUNG
    ====================================================== --}}
    <section class="account-content reviews-content">

        {{-- TIÊU ĐỀ --}}
        <div class="reviews-head">
            <h1>Đánh giá của tôi</h1>
        </div>


        {{-- =================================================
             THÔNG BÁO
        ================================================== --}}
        @if(session('success'))
            <div class="refund-alert refund-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="refund-alert refund-alert-warning">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif


        {{-- =================================================
             TABS
        ================================================== --}}
        <div class="reviews-tabs">

            <button
                type="button"
                class="reviews-tab active"
                data-review-tab="pending"
            >
                Chưa đánh giá

                <span class="review-count">
                    {{ $pendingCount }}
                </span>
            </button>


            <button
                type="button"
                class="reviews-tab"
                data-review-tab="reviewed"
            >
                Đã đánh giá

                <span class="review-count">
                    {{ $reviewedCount }}
                </span>
            </button>

        </div>


        {{-- =================================================
             TAB: CHƯA ĐÁNH GIÁ
        ================================================== --}}
        <div
            class="reviews-tab-content active"
            data-review-content="pending"
        >

            @forelse($pendingReviews as $item)

                <div class="review-order-card">

                    {{-- HEADER ĐƠN HÀNG --}}
                    <div class="review-order-header">

                        <div class="review-order-info">

                            <span>
                                Đơn hàng:
                                <strong>
                                    #{{ $item->MaDonHang }}
                                </strong>
                            </span>

                            <span>
                                Ngày nhận:
                                <strong>
                                    {{ \Carbon\Carbon::parse($item->NgayNhan)->format('d/m/Y') }}
                                </strong>
                            </span>

                        </div>


                        <span class="review-order-status">
                            Chưa đánh giá
                        </span>

                    </div>


                    {{-- SẢN PHẨM --}}
                    <div class="review-product">

                        {{-- ẢNH --}}
                        <div class="review-product-image">

                            @if(!empty($item->HinhAnh))

                                <img
                                    src="{{ asset('storage/' . $item->HinhAnh) }}"
                                    alt="{{ $item->TenSanPham }}"
                                >

                            @else

                                <div class="review-image-empty">
                                    Không có ảnh
                                </div>

                            @endif

                        </div>


                        {{-- THÔNG TIN --}}
                        <div class="review-product-info">

                            <h3>
                                {{ $item->TenSanPham }}
                            </h3>

                            <p>
                                Kích thước:
                                <strong>
                                    {{ $item->KichThuoc ?: '-' }}
                                </strong>

                                &nbsp;·&nbsp;

                                Màu:
                                <strong>
                                    {{ $item->MauSac ?: '-' }}
                                </strong>
                            </p>

                        </div>


                        {{-- NÚT ĐÁNH GIÁ --}}
                        <button
                            type="button"
                            class="review-action-btn"
                            data-review-open

                            data-product-id="{{ $item->SanPhamID }}"

                            data-order-id="{{ $item->DonHangID }}"

                            data-product-name="{{ $item->TenSanPham }}"

                            data-product-size="{{ $item->KichThuoc }}"

                            data-product-color="{{ $item->MauSac }}"

                            data-product-image="{{ !empty($item->HinhAnh) ? asset('storage/' . $item->HinhAnh) : '' }}"
                        >
                            Đánh giá
                        </button>

                    </div>

                </div>

            @empty

                <div class="review-empty">
                    Bạn chưa có sản phẩm nào cần đánh giá.
                </div>

            @endforelse

        </div>


        {{-- =================================================
             TAB: ĐÃ ĐÁNH GIÁ
        ================================================== --}}
        <div
            class="reviews-tab-content"
            data-review-content="reviewed"
        >

            @forelse($reviewedReviews as $item)

                <div class="review-order-card reviewed-card">

                    {{-- HEADER --}}
                    <div class="review-order-header">

                        <div class="review-order-info">

                            <span>
                                Đơn hàng:
                                <strong>
                                    #{{ $item->MaDonHang }}
                                </strong>
                            </span>

                            <span>
                                Ngày nhận:
                                <strong>
                                    {{ \Carbon\Carbon::parse($item->NgayNhan)->format('d/m/Y') }}
                                </strong>
                            </span>

                        </div>


                        <span class="review-order-status reviewed">
                            Đã đánh giá
                        </span>

                    </div>


                    {{-- SẢN PHẨM --}}
                    <div class="review-product">

                        {{-- ẢNH --}}
                        <div class="review-product-image">

                            @if(!empty($item->HinhAnh))

                                <img
                                    src="{{ asset('storage/' . $item->HinhAnh) }}"
                                    alt="{{ $item->TenSanPham }}"
                                >

                            @else

                                <div class="review-image-empty">
                                    Không có ảnh
                                </div>

                            @endif

                        </div>


                        {{-- THÔNG TIN --}}
                        <div class="review-product-info">

                            <h3>
                                {{ $item->TenSanPham }}
                            </h3>

                            <p>
                                Kích thước:
                                <strong>
                                    {{ $item->KichThuoc ?: '-' }}
                                </strong>

                                &nbsp;·&nbsp;

                                Màu:
                                <strong>
                                    {{ $item->MauSac ?: '-' }}
                                </strong>
                            </p>


                            {{-- SAO --}}
                            <div class="review-stars-small">

                                @for($i = 1; $i <= 5; $i++)

                                    <span
                                        class="{{ $i <= $item->SoSao ? 'active' : '' }}"
                                    >
                                        ★
                                    </span>

                                @endfor

                            </div>

                        </div>

                    </div>


                    {{-- NỘI DUNG ĐÁNH GIÁ --}}
                    @if(!empty($item->NoiDung))

                        <div class="review-content-text">
                            {{ $item->NoiDung }}
                        </div>

                    @endif

                </div>

            @empty

                <div class="review-empty">
                    Bạn chưa có đánh giá nào.
                </div>

            @endforelse

        </div>

    </section>

</main>


{{-- =========================================================
     POPUP ĐÁNH GIÁ
========================================================= --}}

<dialog
    class="review-dialog"
    id="reviewDialog"
>

    <form
        method="POST"
        action="{{ route('reviews.store') }}"
    >

        @csrf


        {{-- =================================================
             HEADER POPUP
        ================================================== --}}
        <div class="review-dialog-header">

            <h2>
                Đánh giá sản phẩm
            </h2>

            <button
                type="button"
                class="review-dialog-close"
                data-review-close
                aria-label="Đóng"
            >
                ×
            </button>

        </div>


        {{-- =================================================
             THÔNG TIN SẢN PHẨM
        ================================================== --}}
        <div class="review-dialog-product">

            <div class="review-dialog-image">

                <img
                    id="reviewProductImage"
                    src=""
                    alt="Sản phẩm"
                >

            </div>


            <div class="review-dialog-product-info">

                <h3 id="reviewProductName">
                    Sản phẩm
                </h3>

                <p>
                    Kích thước:
                    <strong id="reviewProductSize">
                        -
                    </strong>

                    &nbsp;·&nbsp;

                    Màu:
                    <strong id="reviewProductColor">
                        -
                    </strong>
                </p>

            </div>

        </div>


        {{-- =================================================
             HIDDEN DATA
        ================================================== --}}

        <input
            type="hidden"
            name="SanPhamID"
            id="reviewProductId"
        >

        <input
            type="hidden"
            name="DonHangID"
            id="reviewOrderId"
        >


        {{-- =================================================
             CHỌN SỐ SAO
        ================================================== --}}
        <div class="review-field">

            <label>
                Đánh giá
            </label>


            <div class="review-stars">

                @for($i = 1; $i <= 5; $i++)

                    <button
                        type="button"
                        class="review-star"
                        data-star="{{ $i }}"
                        aria-label="{{ $i }} sao"
                    >
                        ★
                    </button>

                @endfor

            </div>


            <input
                type="hidden"
                name="DiemDanhGia"
                id="reviewRating"
            >

        </div>


        {{-- =================================================
             BÌNH LUẬN
        ================================================== --}}
        <div class="review-field">

            <label for="reviewComment">
                Bình luận
            </label>


            <textarea
                id="reviewComment"
                name="BinhLuan"
                maxlength="500"
                placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm..."
            ></textarea>


            <div class="review-character-count">

                <span id="reviewCharCount">
                    0
                </span>

                / 500

            </div>

        </div>


        {{-- =================================================
             BUTTON
        ================================================== --}}
        <div class="review-dialog-actions">

            <button
                type="button"
                class="review-btn-solid"
                data-review-close
            >
                Hủy
            </button>


            <button
                type="submit"
                class="review-btn-outline"
            >
                Gửi đánh giá
            </button>

        </div>

    </form>

</dialog>

@endsection