
@extends('layouts.app')

@section('title', 'Mã khuyến mãi của tôi')

@section('content')
@vite(['resources/css/user/my-promotions.css'])

<main class="account-page">
    @include('partials.account-sidebar')

    <section class="account-content my-promotions-page">
    <div class="my-promotions-title-wrap">
        <h1 class="my-promotions-title">MÃ KHUYẾN MÃI CỦA TÔI</h1>

        <p class="my-promotions-description">
            Nhập mã ở bước thanh toán để được giảm giá.
            Muốn tìm thêm mã?
            <a href="{{ route('user.promotion.index') }}">Xem trang Khuyến mãi</a>
        </p>
    </div>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="my-promotions-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="my-promotions-alert error">
            {{ session('error') }}
        </div>
    @endif

    {{-- Bộ lọc --}}
    <nav class="my-promotions-filters" aria-label="Lọc mã khuyến mãi">
        <a href="{{ route('user.account.promotions', ['filter' => 'all']) }}"
           class="{{ $filter === 'all' ? 'active' : '' }}">
            Tất cả ({{ $promotionCounts['all'] }})
        </a>

        <a href="{{ route('user.account.promotions', ['filter' => 'unused']) }}"
           class="{{ $filter === 'unused' ? 'active' : '' }}">
            Chưa dùng ({{ $promotionCounts['unused'] }})
        </a>

        <a href="{{ route('user.account.promotions', ['filter' => 'used']) }}"
           class="{{ $filter === 'used' ? 'active' : '' }}">
            Đã dùng ({{ $promotionCounts['used'] }})
        </a>

        <a href="{{ route('user.account.promotions', ['filter' => 'expired']) }}"
           class="{{ $filter === 'expired' ? 'active' : '' }}">
            Hết hạn ({{ $promotionCounts['expired'] }})
        </a>
    </nav>

    {{-- Danh sách mã đã nhận --}}
    <section class="my-promotions-list">
        @forelse ($promotions as $promotion)
            @php
                $start = \Illuminate\Support\Carbon::parse($promotion->NgayBatDau)->startOfDay();
                $end = \Illuminate\Support\Carbon::parse($promotion->NgayHetHan)->endOfDay();
                $now = now();

                if ((int) $promotion->DaSuDung === 1) {
                    $status = 'Đã dùng';
                    $statusClass = 'used';
                } elseif ($now->gt($end)) {
                    $status = 'Hết hạn';
                    $statusClass = 'expired';
                } elseif ($now->lt($start)) {
                    $status = 'Sắp diễn ra';
                    $statusClass = 'upcoming';
                } else {
                    $status = 'Chưa dùng';
                    $statusClass = 'unused';
                }
            @endphp

            <article class="my-promotion-card">
                <div class="my-promotion-discount">
                    <strong>{{ rtrim(rtrim(number_format((float) $promotion->GiaTriGiam, 2, '.', ''), '0'), '.') }}%</strong>
                </div>

                <div class="my-promotion-content">
                    <div class="my-promotion-card-heading">
                        <h2>{{ $promotion->MaCode }}</h2>
                        <span class="my-promotion-status {{ $statusClass }}">
                            {{ $status }}
                        </span>
                    </div>

                    <p>
                        Giảm {{ rtrim(rtrim(number_format((float) $promotion->GiaTriGiam, 2, '.', ''), '0'), '.') }}%
                        cho đơn từ
                        {{ number_format((float) $promotion->GiaTriDonHangToiThieu, 0, ',', '.') }}đ
                    </p>

                    <p class="my-promotion-date">
                        Nhận ngày {{ \Illuminate\Support\Carbon::parse($promotion->NgayCap)->format('d/m/Y') }}
                    </p>

                    <p class="my-promotion-date">
                        HSD: {{ $end->format('d/m/Y') }}
                    </p>

                    <div class="my-promotion-actions">
                        <button
                            type="button"
                            class="my-promotion-copy"
                            data-copy-code="{{ $promotion->MaCode }}"
                        >
                            Sao chép mã
                        </button>

                        @if ((int) $promotion->DaSuDung === 0 && $now->between($start, $end))
                            <a href="{{ route('user.account.promotions') }}"
                               class="my-promotion-use">
                                Dùng ngay
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="my-promotions-empty">
                <p>Bạn chưa có mã khuyến mãi nào trong mục này.</p>
                <a href="{{ route('user.promotion.index') }}">Khám phá khuyến mãi</a>
            </div>
        @endforelse
    </section>
    </section>
</main>

<script>
document.addEventListener('click', async function (event) {
    const button = event.target.closest('[data-copy-code]');

    if (!button) return;

    const code = button.dataset.copyCode;

    try {
        await navigator.clipboard.writeText(code);
        button.textContent = 'Đã sao chép!';
    } catch (error) {
        window.prompt('Sao chép mã giảm giá:', code);
    }
});
</script>
@endsection
