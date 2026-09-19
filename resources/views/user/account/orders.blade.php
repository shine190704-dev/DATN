@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@push('styles')
    @vite('resources/css/user/order.css')
@endpush

@section('content')

@php
    // Mã trạng thái trong DB -> nhãn hiển thị
    $statusLabels = [
        'MoiTao'         => 'Đơn mới',
        'ChoXacNhan'     => 'Đơn mới',
        'Chờ xác nhận'   => 'Đơn mới',

        'DaXacNhan'      => 'Đã xác nhận',
        'Đã xác nhận'    => 'Đã xác nhận',

        'DangGiao'       => 'Đang giao',
        'DangGiaoHang'   => 'Đang giao',
        'Đang giao hàng' => 'Đang giao',

        'DaGiao'         => 'Đã giao',
        'Đã giao'        => 'Đã giao',

        'HoanThanh'      => 'Đã nhận',

        'DaHuy'          => 'Đã hủy',
        'Đã hủy'         => 'Đã hủy',
    ];
@endphp


<main class="account-page">

    {{-- SIDEBAR --}}
    @include('partials.account-sidebar')


    {{-- NỘI DUNG --}}
    <section class="account-content order-list-content">

        <div class="account-title-wrap">
            <h1 class="account-page-title">Đơn hàng của tôi</h1>
        </div>


        {{-- THÔNG BÁO --}}
        @if(session('success'))
            <div class="order-alert order-alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="order-alert order-alert-error">
                {{ session('error') }}
            </div>
        @endif


        {{-- BỘ LỌC TRẠNG THÁI --}}
        <div class="order-filter">

            {{-- TẤT CẢ --}}
            <a href="{{ route('order.index') }}"
               class="{{ request('status') ? '' : 'active' }}">
                Tất cả
            </a>


            {{-- ĐƠN MỚI --}}
            <a href="{{ route('order.index', ['status' => 'ChoXacNhan']) }}"
               class="{{ request('status') === 'ChoXacNhan' ? 'active' : '' }}">
                Đơn mới
            </a>


            {{-- ĐÃ XÁC NHẬN --}}
            <a href="{{ route('order.index', ['status' => 'DaXacNhan']) }}"
               class="{{ request('status') === 'DaXacNhan' ? 'active' : '' }}">
                Đã xác nhận
            </a>


              {{-- ĐANG GIAO --}}
            <a href="{{ route('order.index', ['status' => 'DangGiao']) }}"
                class="{{ request('status') === 'DangGiao' ? 'active' : '' }}">
                Đang giao
            </a>


            {{-- ĐÃ NHẬN --}}
            <a href="{{ route('order.index', ['status' => 'DaGiao']) }}"
               class="{{ request('status') === 'DaGiao' ? 'active' : '' }}">
                Đã nhận
            </a>


            {{-- ĐÃ HỦY --}}
            <a href="{{ route('order.index', ['status' => 'DaHuy']) }}"
               class="{{ request('status') === 'DaHuy' ? 'active' : '' }}">
                Đã hủy
            </a>

        </div>


        {{-- DANH SÁCH ĐƠN --}}
        @forelse($orders as $order)

            @php
                // Chỉ đơn mới được hủy
                $canCancel = in_array($order->TrangThai, [
                    'MoiTao',
                    'ChoXacNhan',
                    'Chờ xác nhận',
                ]);

                // Đơn đã giao, chờ khách xác nhận đã nhận
                $canReceive = in_array($order->TrangThai, [
                    'DangGiao',
                    'DangGiaoHang',
                    'Đang giao hàng',
                ]);
                

                // Đơn khách đã xác nhận nhận hàng
                $canReview = $order->TrangThai === 'HoanThanh';
            @endphp


            {{-- THẺ ĐƠN HÀNG --}}
            <div class="order-card">


                {{-- HEADER --}}
                <div class="order-card-header">

                    <div>
                        Mã đơn
                        <strong>
                            #{{ $order->MaDonHang }}
                        </strong>
                    </div>


                    <div>
                        Đặt ngày
                        <strong>
                            {{ \Carbon\Carbon::parse($order->NgayTao)->format('d/m/Y') }}
                        </strong>
                    </div>


                    {{-- BADGE TRẠNG THÁI --}}
                    <span class="order-status">
                        {{ $statusLabels[$order->TrangThai] ?? $order->TrangThai }}
                    </span>

                </div>


                {{-- SẢN PHẨM --}}
                <div class="order-products">

                    @foreach($order->items as $item)

                        <div class="order-product">


                            {{-- HÌNH ẢNH --}}
                            <div class="order-product-image">

                                @if($item->HinhAnh)

                                    <img
                                        src="{{ asset('images/' . $item->HinhAnh) }}"
                                        alt="{{ $item->TenSanPham }}"
                                    >

                                @endif

                            </div>


                            {{-- THÔNG TIN SẢN PHẨM --}}
                            <div class="order-product-info">

                                <strong>
                                    {{ $item->TenSanPham }}
                                </strong>

                                <span>
                                    Số lượng: {{ $item->SoLuong }}
                                </span>


                                @if($item->KichThuoc)

                                    <span>
                                        Kích thước: {{ $item->KichThuoc }}
                                    </span>

                                @endif


                                @if($item->MauSac)

                                    <span>
                                        Màu: {{ $item->MauSac }}
                                    </span>

                                @endif

                            </div>


                            {{-- GIÁ --}}
                            <div class="order-product-price">

                                {{ number_format(
                                    $item->GiaTaiThoiDiemMua * $item->SoLuong,
                                    0,
                                    ',',
                                    '.'
                                ) }} VND

                            </div>

                        </div>

                    @endforeach

                </div>


                {{-- FOOTER --}}
                <div class="order-card-footer">


                    {{-- TỔNG TIỀN --}}
                    <div class="order-total">

                        <span>
                            TỔNG TIỀN:
                        </span>

                        <strong>
                            {{ number_format(
                                $order->TongTien,
                                0,
                                ',',
                                '.'
                            ) }} VND
                        </strong>

                    </div>


                    {{-- CÁC NÚT --}}
                    <div class="order-actions">


                        {{-- CHI TIẾT --}}
                        <a
                            href="{{ route('tracking.index') }}"
                            class="order-detail-btn"
                        >
                            Chi tiết
                        </a>


                        {{-- HỦY ĐƠN
                             CHỈ HIỆN KHI TRẠNG THÁI = MoiTao
                        --}}
                        @if($canCancel)

                            <button
                                type="button"
                                class="order-cancel-btn"
                                data-cancel-url="{{ route('order.cancel', $order->DonHangID) }}"
                            >
                                Hủy đơn
                            </button>

                        @endif


                        {{-- ĐÃ NHẬN HÀNG
                             CHỈ HIỆN KHI TRẠNG THÁI = DaGiao
                        --}}
                        @if($canReceive)

                            <button
                                type="button"
                                class="order-receive-btn"
                                data-receive-url="{{ route('order.receive', $order->DonHangID) }}"
                            >
                                Đã nhận hàng
                            </button>

                        @endif


                        {{-- ĐÁNH GIÁ
                             CHỈ HIỆN KHI TRẠNG THÁI = HoanThanh
                        --}}
                        @if($canReview)

                            <a
                                href="{{ Route::has('review.index') ? route('review.index') : '#' }}"
                                class="order-review-btn"
                            >
                                Đánh giá
                            </a>

                        @endif

                    </div>

                </div>

            </div>


        @empty

            <div class="order-empty">

                <p>
                    Bạn chưa có đơn hàng nào.
                </p>

            </div>

        @endforelse

    </section>

</main>


{{-- =========================================================
     HỘP XÁC NHẬN HỦY ĐƠN
========================================================= --}}

<dialog
    class="order-confirm"
    id="cancelDialog"
>

    <form
        method="POST"
        id="cancelForm"
    >

        @csrf

        <p>
            Bạn có chắc muốn hủy đơn hàng này không?
        </p>


        <div class="order-confirm-actions">

            <button
                type="button"
                class="order-confirm-no"
                id="cancelClose"
            >
                Không
            </button>


            <button
                type="submit"
                class="order-cancel-btn"
            >
                Hủy đơn
            </button>

        </div>

    </form>

</dialog>


{{-- =========================================================
     HỘP XÁC NHẬN ĐÃ NHẬN HÀNG
========================================================= --}}

<dialog
    class="order-confirm"
    id="receiveDialog"
>

    <form
        method="POST"
        id="receiveForm"
    >

        @csrf

        <p>
            Bạn xác nhận đã nhận được hàng?
        </p>


        <div class="order-confirm-actions">

            <button
                type="button"
                class="order-confirm-no"
                id="receiveClose"
            >
                Không
            </button>


            <button
                type="submit"
                class="order-receive-btn"
            >
                Đã nhận hàng
            </button>

        </div>

    </form>

</dialog>



@endsection