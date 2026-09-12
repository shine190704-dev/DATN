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
        <a
            href="{{ route('category.show', $danhMuc->DanhMucID) }}"
            class="{{ request()->routeIs('category.show') && request()->route('id') == $danhMuc->DanhMucID ? 'active' : '' }}"
        >
            {{ mb_strtoupper($danhMuc->TenDanhMuc, 'UTF-8') }}
        </a>
    @endforeach

    {{-- VỀ CHÚNG TÔI --}}
    <a href="#">
        VỀ CHÚNG TÔI
    </a>

</nav>