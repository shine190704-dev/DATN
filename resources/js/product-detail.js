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
    let selectedVariant = variantData[0] || null;

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

        selectedVariant = variant;

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

    const addCartButton = document.querySelector('.product-add-cart');
    const toast = document.querySelector('.cart-toast');
    let toastTimer;

    function showCartToast(message) {
        if (!toast) {
            return;
        }

        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            toast.classList.remove('show');
        }, 2500);
    }

    function updateCartBadge(count) {
        const cartIcon = document.querySelector('.header-cart-icon');

        if (!cartIcon) {
            return;
        }

        let badge = cartIcon.querySelector('.cart-count-badge');

        if (!badge) {
            badge = document.createElement('span');
            badge.className = 'cart-count-badge';
            cartIcon.appendChild(badge);
        }

        badge.textContent = count;
        badge.hidden = Number(count) < 1;
    }

    addCartButton?.addEventListener('click', async function () {
        if (!selectedVariant) {
            showCartToast('Sản phẩm chưa có biến thể để thêm vào giỏ.');
            return;
        }

        addCartButton.disabled = true;

        try {
            const response = await fetch(addCartButton.dataset.cartUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': addCartButton.dataset.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    BienTheID: selectedVariant.BienTheID,
                    SoLuong: selectedQuantity
                })
            });

            const data = await response.json();

            if (response.status === 401) {
                showCartToast(data.message);
                setTimeout(function () {
                    window.location.href = '/dang-nhap';
                }, 1200);
                return;
            }

            if (!response.ok || !data.success) {
                showCartToast(data.message || 'Không thể thêm sản phẩm vào giỏ.');
                return;
            }

            updateCartBadge(data.cartCount);
            showCartToast(data.message);
        } catch (error) {
            console.error('Lỗi giỏ hàng:', error);
            showCartToast('Không thể kết nối đến máy chủ.');
        } finally {
            addCartButton.disabled = false;
        }
    });

    updateVariant();
});
