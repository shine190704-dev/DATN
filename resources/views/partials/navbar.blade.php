
<nav class="main-nav">
    <a href="/">
        KHÁM PHÁ TẤT CẢ
    </a>
    <a href="#">
        SẢN PHẨM MỚI
    </a>
    <!-- LẤY DANH MỤC TỪ DATABASE -->
    @foreach($danhMucs as $danhMuc)
        <a href="{{ route('category.show', $danhMuc->DanhMucID) }}">
            {{ mb_strtoupper($danhMuc->TenDanhMuc, 'UTF-8') }}
        </a>
    @endforeach
    
    <a href="#">
        VỀ CHÚNG TÔI
    </a>

</nav>