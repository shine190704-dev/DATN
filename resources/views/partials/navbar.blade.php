<nav class="main-nav">

    {{-- KHÁM PHÁ TẤT CẢ --}}
    <a
        href="{{ route('product.all') }}"
        class="{{ request()->routeIs('product.all') ? 'active' : '' }}"
    >
        KHÁM PHÁ TẤT CẢ
    </a>

    {{-- SẢN PHẨM MỚI --}}
    <a
        href="{{ route('product.new') }}"
        class="{{ request()->routeIs('product.new') ? 'active' : '' }}"
    >
        SẢN PHẨM MỚI
    </a>

    {{-- DANH MỤC --}}
    @foreach($danhMucs as $danhMuc)
        @php
            $isPromotionCategory = mb_strtolower(trim($danhMuc->TenDanhMuc), 'UTF-8') === 'khuyến mãi';
            $categoryUrl = $isPromotionCategory
                ? route('user.promotion.index')
                : route('category.show', $danhMuc->DanhMucID);
            $isCategoryActive = $isPromotionCategory
                ? request()->routeIs('user.promotion.*')
                : request()->routeIs('category.show') && request()->route('id') == $danhMuc->DanhMucID;
        @endphp
        <a
            href="{{ $categoryUrl }}"
            class="{{ $isCategoryActive ? 'active' : '' }}"
        >
            {{ mb_strtoupper($danhMuc->TenDanhMuc, 'UTF-8') }}
        </a>
    @endforeach

    {{-- VỀ CHÚNG TÔI --}}
    <a href="#">
        VỀ CHÚNG TÔI
    </a>
</nav>