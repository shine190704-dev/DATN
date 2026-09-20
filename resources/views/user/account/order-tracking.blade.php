@extends('layouts.app')

@section('title', 'Theo dõi đơn hàng')

@push('styles')
    @vite('resources/css/user/order-tracking.css')
@endpush

@section('content')

<main class="account-page">

    {{-- SIDEBAR --}}
    @include('partials.account-sidebar')

    {{-- NỘI DUNG --}}
    <section class="account-content order-tracking-content">

        <div class="account-title-wrap">
            <h1 class="account-page-title">Theo dõi đơn hàng của bạn</h1>
        </div>

        @forelse($orders as $order)

            @php
                // Trạng thái trong DB -> vị trí bước (0..3)
                // Đối chiếu với các giá trị thật của cột DonHang.TrangThai
                $statusMap = [
                    'MoiTao'             => 0,
                    'ChoXacNhan'         => 0,
                    'Chờ xác nhận'       => 0,
                    'DaXacNhan'          => 1,
                    'Đã xác nhận'        => 1,
                    'DangGiao'           => 2,
                    'DangGiaoHang'       => 2,
                    'Đang giao hàng'     => 2,
                    'DaGiao'             => 3,
                    'HoanThanh'          => 3,
                    'Đã giao'            => 3,
                    'Đã giao thành công' => 3,
                ];
                $stepIndex = $statusMap[$order->TrangThai] ?? 0;

                // ChiTietDonHang.HinhAnh lưu tên file trong public/images/
                $imgDir = 'images/';

                $steps = [
                    ['Đã đặt hàng',        'Đơn hàng của bạn đã được ghi nhận.'],
                    ['Đã xác nhận',        'Shop đã xác nhận và đang chuẩn bị hàng.'],
                    ['Đang giao hàng',     'Đơn vị vận chuyển đang giao đến bạn.'],
                    ['Đã giao thành công', 'Đơn hàng đã được giao.'],
                ];

                $subtotal = $order->items->sum(fn ($i) => $i->GiaTaiThoiDiemMua * $i->SoLuong);
                $giamGia = (float) ($order->SoTienGiam ?? 0);
                $maVoucher = $order->MaVoucher ?? null;

                // DonHang không có cột phí ship nên suy ra: Tổng = Tạm tính + Ship - Giảm
                $phiVanChuyen = max(0, $order->TongTien - $subtotal + $giamGia);
            @endphp

            <div class="tracking-order-card">

                <p class="tracking-order-code">
                        Mã đơn <strong>#{{ $order->MaDonHang }}</strong>
                </p>

                    @if($order->items->isNotEmpty())
                        <p class="tracking-order-products">
                            {{ $order->items->first()->TenSanPham }}
                         @if($order->items->count() > 1)
                                và {{ $order->items->count() - 1 }} sản phẩm khác
                            @endif
                        </p>
                    @endif

                {{-- TIMELINE --}}
                <div class="tracking-timeline">
                    @foreach($steps as $i => [$label, $desc])
                        <div class="tracking-step {{ $i <= $stepIndex ? 'done' : 'pending' }}">
                            <div class="tracking-dot-row"><span class="tracking-dot"></span></div>
                            <h4>{{ $label }}</h4>
                            <p>{{ $desc }}</p>
                            @if($i === 0)
                                <p>{{ \Carbon\Carbon::parse($order->NgayTao)->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- XEM CHI TIẾT --}}
                <details class="tracking-detail">
                    <summary>Xem chi tiết</summary>

                    <div class="tracking-detail-body">

                        <div class="table-scroll">
                            <table class="tracking-items">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Tổng tiên</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="item-cell">
                                                    <img src="{{ asset($imgDir . $item->HinhAnh) }}" alt="{{ $item->TenSanPham }}">
                                                    <span>
                                                        {{ $item->TenSanPham }}
                                                        @if($item->MauSac || $item->KichThuoc)
                                                            <small class="item-variant">
                                                                {{ collect([$item->MauSac, $item->KichThuoc])->filter()->implode(' · ') }}
                                                            </small>
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td>{{ $item->SoLuong }}</td>
                                            <td>{{ number_format($item->GiaTaiThoiDiemMua, 0, ',', '.') }}đ</td>
                                            <td>{{ number_format($item->GiaTaiThoiDiemMua * $item->SoLuong, 0, ',', '.') }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tracking-info">
                            <p><span>Ngày đặt:</span> <strong>{{ \Carbon\Carbon::parse($order->NgayTao)->format('d/m/Y') }}</strong></p>
                            <p><span>Người nhận:</span> <strong>{{ $order->TenNguoiNhan }}</strong></p>
                            <p><span>Số điện thoại:</span> <strong>{{ $order->SoDienThoaiNguoiNhan }}</strong></p>
                            <p><span>Địa chỉ:</span> <strong>{{ $order->DiaChiNhanHang }}</strong></p>
                            <p><span>Thanh toán:</span> <strong>{{ strtoupper($order->PhuongThucThanhToan) }}</strong></p>
                        </div>

                        <div class="tracking-summary">
                            <p>
                                <span>Tạm tính</span>
                                <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
                            </p>

                            <p>
                                <span>Phí vận chuyển</span>
                                <strong>{{ $phiVanChuyen > 0 ? number_format($phiVanChuyen, 0, ',', '.') . 'đ' : 'Miễn phí' }}</strong>
                            </p>

                            @if($giamGia > 0)
                                <p>
                                    <span>Voucher
                                        @if($maVoucher)
                                            <em class="voucher-code">{{ $maVoucher }}</em>
                                        @endif
                                    </span>
                                    <strong>-{{ number_format($giamGia, 0, ',', '.') }}đ</strong>
                                </p>
                            @endif

                            <p class="tracking-total">
                                <span>Tổng tiền</span>
                                <strong>{{ number_format($order->TongTien, 0, ',', '.') }} VND</strong>
                            </p>
                        </div>

                    </div>
                </details>

            </div>

        @empty

            <div class="order-tracking-empty">
                <p>Bạn chưa có đơn hàng nào.</p>
            </div>

        @endforelse

    </section>

</main>

@endsection