@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')

<main class="order-success-page">

    <div class="order-success-container">

        <div class="order-success-icon">
            ✓
        </div>

        <h1 class="order-success-title">
            Đặt hàng thành công!
        </h1>

        <p class="order-success-desc">
            Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đang được xử lý.
        </p>

        <div class="order-success-code">
            Mã đơn hàng: <strong>{{ $donHang->MaDonHang }}</strong>
        </div>

        <div class="order-success-info">

            <div class="order-success-row">
                <span>Người nhận:</span>
                <strong>{{ $donHang->TenNguoiNhan }}</strong>
            </div>

            <div class="order-success-row">
                <span>Số điện thoại:</span>
                <strong>{{ $donHang->SoDienThoaiNguoiNhan }}</strong>
            </div>

            <div class="order-success-row">
                <span>Địa chỉ giao hàng:</span>
                <strong>{{ $donHang->DiaChiNhanHang }}</strong>
            </div>

            <div class="order-success-row">
                <span>Phương thức thanh toán:</span>
                <strong>
                    {{ $donHang->PhuongThucThanhToan === 'cod' ? 'Thanh toán khi nhận hàng' : 'Chuyển khoản ngân hàng' }}
                </strong>
            </div>

            <div class="order-success-row order-success-total">
                <span>Tổng tiền:</span>
                <strong>{{ number_format($donHang->TongTien, 0, ',', '.') }} VND</strong>
            </div>

        </div>

        <div class="order-success-products">

        <!-- =====    Nếu muốn hiện sản phẩm thì ở đây nè ===-->

        </div>

        <a href="{{ route('home') }}" class="order-success-btn">
            Về trang chủ
        </a>

    </div>

</main>

@endsection