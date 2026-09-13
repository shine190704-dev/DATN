document.addEventListener('DOMContentLoaded', function () {
    const detailPage = document.querySelector('.product-detail-page');
    const variantData = detailPage
        ? JSON.parse(detailPage.dataset.variants || '[]')
        : [];
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = Array.from(
        document.querySelectorAll('.product-thumbnail')
    );
    const previousButton = document.querySelector('.product-gallery-prev');
    const nextButton = document.querySelector('.product-gallery-next');
    let currentIndex = 0;

    function showProductImage(index) {
        if (!mainImage || thumbnails.length === 0) {
            return;
        }

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

    const sizeButtons = Array.from(document.querySelectorAll('.variant-size'));
    const colorButtons = Array.from(document.querySelectorAll('.variant-color'));
    const priceElement = document.getElementById('variantPrice');
    const quantityElement = document.getElementById('selectedQuantity');
    const decreaseButton = document.querySelector('.quantity-decrease');
    const increaseButton = document.querySelector('.quantity-increase');
    let selectedQuantity = 1;
    let selectedStock = Number(variantData[0]?.SoLuong || 0);

    function updateVariant() {
        const selectedSize = document.querySelector('.variant-size.active')?.dataset.size;
        const selectedColor = document.querySelector('.variant-color.active')?.dataset.color;
        const variant = variantData.find(function (item) {
            return item.KichThuoc === selectedSize && item.MauSac === selectedColor;
        });

        if (!variant) {
            return;
        }

        if (priceElement) {
            priceElement.textContent = Number(variant.GiaBienThe).toLocaleString('vi-VN') + ' VND';
        }

        if (quantityElement) {
            selectedStock = Number(variant.SoLuong || 0);
            selectedQuantity = Math.min(selectedQuantity, Math.max(selectedStock, 1));
            quantityElement.textContent = selectedQuantity;
        }
    }

    sizeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            sizeButtons.forEach(function (item) {
                item.classList.remove('active');
            });
            button.classList.add('active');
            updateVariant();
        });
    });

    colorButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            colorButtons.forEach(function (item) {
                item.classList.remove('active');
            });
            button.classList.add('active');
            updateVariant();
        });
    });

    decreaseButton?.addEventListener('click', function () {
        selectedQuantity = Math.max(1, selectedQuantity - 1);
        quantityElement.textContent = selectedQuantity;
    });

    increaseButton?.addEventListener('click', function () {
        if (selectedQuantity < selectedStock) {
            selectedQuantity += 1;
            quantityElement.textContent = selectedQuantity;
        }
    });

    updateVariant();
});
