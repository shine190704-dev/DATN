document.addEventListener('DOMContentLoaded', function () {
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = Array.from(
        document.querySelectorAll('.product-thumbnail')
    );
    const previousButton = document.querySelector('.product-gallery-prev');
    const nextButton = document.querySelector('.product-gallery-next');
    let currentIndex = 0;

    if (!mainImage || thumbnails.length === 0) {
        return;
    }

    function showProductImage(index) {
        currentIndex = (index + thumbnails.length) % thumbnails.length;
        const thumbnail = thumbnails[currentIndex];

        mainImage.src = thumbnail.dataset.imageUrl;
        thumbnails.forEach(function (item) {
            item.classList.toggle('active', item === thumbnail);
        });
    }

    thumbnails.forEach(function (thumbnail, index) {
        thumbnail.addEventListener('click', function () {
            showProductImage(index);
        });
    });

    if (previousButton) {
        previousButton.addEventListener('click', function () {
            showProductImage(currentIndex - 1);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            showProductImage(currentIndex + 1);
        });
    }

    showProductImage(0);
});
