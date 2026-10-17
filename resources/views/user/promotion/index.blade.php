 @extends('layouts.app')

@section('title', 'Khuyến mãi')

@section('content')
@vite([
    'resources/css/user/promotion.css'
])

<main class="user-promotion-page">
    <h1 class="user-promotion-title">Khuyến mãi</h1>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="user-promotion-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="user-promotion-alert error">
            {{ session('error') }}
        </div>
    @endif

    {{-- Danh sách mã --}}
    <div class="user-promotion-list">
        @forelse ($promotions as $promotion)
            @php
                $now = now();
                $start = \Illuminate\Support\Carbon::parse($promotion->NgayBatDau)->startOfDay();
                $end = \Illuminate\Support\Carbon::parse($promotion->NgayHetHan)->endOfDay();

                $claimed = in_array(
                    (int) $promotion->MaGiamGiaID,
                    $claimedPromotionIds,
                    true
                );

                $issued = (int) $promotion->SoLuongDaCap;
                $quantity = (int) $promotion->SoLuongPhatHanh;

                if ($now->gt($end)) {
                    $status = 'Đã hết hạn';
                    $statusClass = 'expired';
                } elseif ($claimed) {
                    $status = 'Đã nhận';
                    $statusClass = 'claimed';
                } elseif ($now->lt($start)) {
                    $status = 'Sắp diễn ra';
                    $statusClass = 'upcoming';
                } elseif ($issued >= $quantity) {
                    $status = 'Hết lượt nhận';
                    $statusClass = 'depleted';
                } else {
                    $status = 'Có thể nhận';
                    $statusClass = 'available';
                }
            @endphp

            <article class="user-promotion-card">
                <div class="user-promotion-discount">
                    <span>{{ rtrim(rtrim(number_format((float) $promotion->GiaTriGiam, 2, '.', ''), '0'), '.') }}%</span>
                </div>

                <div class="user-promotion-info">
                    <h2>{{ $promotion->MaCode }}</h2>

                    <p>
                        Giảm {{ rtrim(rtrim(number_format((float) $promotion->GiaTriGiam, 2, '.', ''), '0'), '.') }}%
                        cho đơn từ
                        {{ number_format((float) $promotion->GiaTriDonHangToiThieu, 0, ',', '.') }}đ
                    </p>

                    <p>
                        Bắt đầu: {{ $start->format('d/m/Y') }}
                    </p>

                    <p>
                        HSD: {{ $end->format('d/m/Y') }}
                    </p>

                    <div class="user-promotion-action">
                        @if ($now->gt($end))
                            <span class="user-promotion-status {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        @elseif ($claimed)
                            <span class="user-promotion-status {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        @elseif ($now->lt($start) || $issued >= $quantity)
                            <span class="user-promotion-status {{ $statusClass }}">
                                {{ $status }}
                            </span>
                        @elseif (session('NguoiDungID'))
                            <form
                                action="{{ route('user.promotion.claim', ['id' => $promotion->MaGiamGiaID]) }}"
                                method="POST"
                            >
                                @csrf
                                <button type="submit" class="user-promotion-claim">
                                    Nhận mã
                                </button>
                            </form>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="user-promotion-claim"
                                title="Đăng nhập để nhận mã"
                            >
                                Đăng nhập để nhận
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <p class="user-promotion-empty">
                Hiện chưa có chương trình khuyến mãi nào.
            </p>
        @endforelse
    </div>
</main>
@endsection