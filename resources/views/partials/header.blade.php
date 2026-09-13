<header class="site-header">

    <div class="header-main">

        <!-- SEARCH BÊN TRÁI -->
        <div class="header-search">
            <input type="search" name="search" placeholder="" aria-label="Tìm kiếm">
            <button type="button" aria-label="">
                <img src="{{ asset('images/ICONS/search_icon.png') }}" alt="Tìm kiếm">
            </button>
        </div>

        

        <!-- LOGO Ở GIỮA -->
        <a href="/" class="header-logo">
            <img src="{{ asset('images/ICONS/logo.png') }}" alt="Dollie">
        </a>

        <!-- ICON BÊN PHẢI -->
        <div class="header-actions">
            <a href="{{ route('wishlist.index') }}" class="header-icon">
                <img src="{{ asset('images/ICONS/heart_icon.png') }}" alt="Yêu thích">
            </a>
            <a href="#" class="header-icon">
                <img src="{{ asset('images/ICONS/shoppingcart_icon.png') }}" alt="Giỏ hàng">
            </a>
            <a href="{{ route('profile') }}" class="header-icon">
                <img src="{{ asset('images/ICONS/profile_icon.png') }}" alt="Tài khoản">
            </a>
        </div>

    </div>



</header>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.querySelector('.header-search input');
    const searchButton = document.querySelector('.header-search button');

    if (!searchInput || !searchButton) {
        return;
    }

    function searchProduct() {

        const keyword = searchInput.value.trim();

        // Không nhập gì thì không làm gì
        if (keyword === '') {
            return;
        }

        window.location.href =
            "{{ route('product.search') }}?keyword=" + encodeURIComponent(keyword);
    }

    // Bấm kính lúp
    searchButton.addEventListener('click', function () {
        searchProduct();
    });

    // Nhấn Enter
    searchInput.addEventListener('keydown', function (event) {

        if (event.key === 'Enter') {
            event.preventDefault();
            searchProduct();
        }

    });

});
</script>