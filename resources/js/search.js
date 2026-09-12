document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.querySelector('.header-search input');
    const searchButton = document.querySelector('.header-search button');

    if (!searchInput || !searchButton) {
        return;
    }

    function searchProduct() {

        const keyword = searchInput.value.trim();

        // Không nhập gì thì không tìm kiếm
        if (keyword === '') {
            return;
        }

        window.location.href =
            '/tim-kiem?keyword=' + encodeURIComponent(keyword);
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