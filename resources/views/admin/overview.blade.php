<!DOCTYPE html>
<html lang="vi">

<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Tổng quan - Dollie</title>

    @vite([
        'resources/css/admin/dashboard.css',
        'resources/css/admin/overview.css'
    ])

</head>


<body>

<div class="admin-overview admin-shell">


    {{-- =====================================================
         SIDEBAR
         DÙNG SIDEBAR CHUNG
    ====================================================== --}}

    @include('admin.partials.sidebar')


    {{-- =====================================================
         MAIN
    ====================================================== --}}

    <main class="admin-main">


        {{-- =================================================
             TIÊU ĐỀ
        ================================================== --}}

        <div class="admin-page-title">

            <h1>
                THỐNG KÊ TỔNG QUAN
            </h1>

        </div>


        {{-- =================================================
             BỘ LỌC NGÀY
        ================================================== --}}

        <form
            action="{{ route('admin.overview') }}"
            method="GET"
            class="admin-filter"
        >

            <div class="admin-filter-group">

                <label for="tu_ngay">
                    Từ ngày
                </label>

                <input
                    type="date"
                    id="tu_ngay"
                    name="tu_ngay"
                    value="{{ $tuNgay }}"
                >

            </div>


            <div class="admin-filter-group">

                <label for="den_ngay">
                    Đến ngày
                </label>

                <input
                    type="date"
                    id="den_ngay"
                    name="den_ngay"
                    value="{{ $denNgay }}"
                >

            </div>


            <button
                type="submit"
                class="admin-filter-button"
            >
                Xem thống kê
            </button>

        </form>


        {{-- =================================================
             KHOẢNG THỜI GIAN
        ================================================== --}}

        <div class="admin-viewing-date">

            Đang xem:

            {{ \Carbon\Carbon::parse($tuNgay)->format('d/m/Y') }}

            —

            {{ \Carbon\Carbon::parse($denNgay)->format('d/m/Y') }}

        </div>


        {{-- =================================================
             4 Ô THỐNG KÊ
        ================================================== --}}

        <section class="admin-statistics">


            {{-- DOANH THU --}}

            <div class="admin-stat-card">

                <div class="admin-stat-icon">

                    <svg viewBox="0 0 24 24">

                        <path d="M12 3v18"/>

                        <path
                            d="M16 7.5c0-2-1.5-3-4-3s-4 1-4 3
                               1.5 2.8 4 3.5
                               4 1.5 4 3.5
                               -1.5 3-4 3
                               -4 1-4-3"
                        />

                    </svg>

                </div>


                <div class="admin-stat-content">

                    <strong>
                        {{ number_format($thongKe['doanhThu'], 0, ',', '.') }}₫
                    </strong>

                    <span>
                        Doanh thu
                    </span>

                </div>

            </div>


            {{-- TỔNG ĐƠN HÀNG --}}

            <div class="admin-stat-card">

                <div class="admin-stat-icon">

                    <svg viewBox="0 0 24 24">

                        <path d="M5 9h14l-1 11H6L5 9z"/>

                        <path d="M8 9V7a4 4 0 0 1 8 0v2"/>

                    </svg>

                </div>


                <div class="admin-stat-content">

                    <strong>
                        {{ $thongKe['tongDonHang'] }}
                    </strong>

                    <span>
                        Tổng đơn hàng
                    </span>

                </div>

            </div>


            {{-- KHÁCH HÀNG MỚI --}}

            <div class="admin-stat-card">

                <div class="admin-stat-icon">

                    <svg viewBox="0 0 24 24">

                        <circle
                            cx="9"
                            cy="8"
                            r="3"
                        />

                        <circle
                            cx="17"
                            cy="9"
                            r="2.5"
                        />

                        <path d="M3.5 19c.5-3.5 2.5-5 5.5-5s5 1.5 5.5 5"/>

                        <path d="M14 14c2.5-.5 5 1 6 4"/>

                    </svg>

                </div>


                <div class="admin-stat-content">

                    <strong>
                        {{ $thongKe['khachHangMoi'] }}
                    </strong>

                    <span>
                        Khách hàng mới
                    </span>

                </div>

            </div>


            {{-- HOÀN TIỀN --}}

            <a
                href="#"
                aria-label="Mở trang xử lý hoàn tiền"
                class="admin-stat-card admin-stat-card-link"
            >

                <div class="admin-stat-icon">

                    <svg viewBox="0 0 24 24">

                        <path d="M5 4h14v16l-7-4-7 4z"/>

                        <path d="M9 9h6"/>

                        <path d="M9 12h4"/>

                    </svg>

                </div>


                <div class="admin-stat-content">

                    <strong>
                        {{ $thongKe['hoanTienChoXuLy'] }}
                    </strong>

                    <span>
                        Hoàn tiền chờ xử lý
                    </span>

                </div>

            </a>


        </section>


        {{-- =================================================
             TRẠNG THÁI ĐƠN HÀNG + CẢNH BÁO
        ================================================== --}}

        <section class="admin-middle-section">


            {{-- =================================================
                 TRẠNG THÁI ĐƠN HÀNG
            ================================================== --}}

            <div class="admin-order-status">

                <h2>
                    Trạng thái đơn hàng
                </h2>


                <div class="admin-order-chart-wrapper">


                    {{-- DONUT CHART --}}

                    <div
                        class="admin-donut-chart"
                        style="background: {{ $bieuDoTrangThai }}"
                    >

                        <div class="admin-donut-center"></div>

                    </div>


                    {{-- CHÚ THÍCH --}}

                    <div class="admin-chart-legend">

                        @foreach($trangThaiDonHang as $item)

                            <div class="admin-legend-item">

                                <span
                                    class="admin-legend-dot {{ $item['class'] }}"
                                ></span>


                                <span class="admin-legend-name">
                                    {{ $item['ten'] }}
                                </span>


                                <span class="admin-legend-percent">
                                    {{ $item['phanTram'] }}%
                                </span>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>


            {{-- =================================================
                 CẢNH BÁO HẾT HÀNG
            ================================================== --}}

            <div class="admin-stock-warning">

                <h2>
                    Cảnh báo hết hàng
                </h2>


                <div class="admin-low-stock-card">


                    <div class="admin-card-title-row">

                        <h3>
                            Sản phẩm sắp hết hàng
                        </h3>


                        <a href="#">
                            Xem tất cả
                        </a>

                    </div>


                    <div class="admin-low-stock-list">

                        @forelse($sanPhamSapHet as $item)
                            <div class="admin-low-stock-item">
                                <div>
                                    <strong>{{ $item['ten'] }}</strong>
                                    <span>{{ $item['bienThe'] }}</span>
                                </div>
                                <strong>Còn {{ $item['soLuong'] }}</strong>
                            </div>
                        @empty
                            <p class="admin-low-stock-empty">
                                Hiện tại không có sản phẩm sắp hết hàng.
                            </p>
                        @endforelse

                    </div>

                </div>

            </div>


        </section>


        {{-- =================================================
             SẢN PHẨM BÁN CHẠY / BÁN CHẬM
        ================================================== --}}

        <section class="admin-product-ranking">


            {{-- =================================================
                 BÁN CHẠY
            ================================================== --}}

            <div class="admin-ranking-card">

                <h2>
                    Sản phẩm bán chạy
                </h2>


                @foreach($sanPhamBanChay as $index => $item)

                    <div class="admin-ranking-item">


                        <span class="admin-ranking-number">
                            {{ $index + 1 }}.
                        </span>


                        <div class="admin-ranking-info">

                            <strong>
                                {{ $item['ten'] }}
                            </strong>

                            <span>
                                Đã bán {{ $item['daBan'] }}
                            </span>

                        </div>


                        <strong class="admin-ranking-price">

                            {{ number_format($item['doanhThu'], 0, ',', '.') }}₫

                        </strong>


                    </div>

                @endforeach

            </div>


            {{-- =================================================
                 BÁN CHẬM
            ================================================== --}}

            <div class="admin-ranking-card">

                <h2>
                    Sản phẩm bán chậm
                </h2>


                @foreach($sanPhamBanCham as $index => $item)

                    <div class="admin-ranking-item">


                        <span class="admin-ranking-number">
                            {{ $index + 1 }}.
                        </span>


                        <div class="admin-ranking-info">

                            <strong>
                                {{ $item['ten'] }}
                            </strong>

                            <span>
                                Đã bán {{ $item['daBan'] }}
                            </span>

                        </div>


                        <strong class="admin-ranking-price">

                            {{ number_format($item['doanhThu'], 0, ',', '.') }}₫

                        </strong>


                    </div>

                @endforeach

            </div>


        </section>

    </main>

</div>


</body>

</html>
