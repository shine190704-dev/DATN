document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       TAB: CHƯA ĐÁNH GIÁ / ĐÃ ĐÁNH GIÁ
    ========================================================= */

    const tabs = document.querySelectorAll('[data-review-tab]');
    const tabContents = document.querySelectorAll('[data-review-content]');

    tabs.forEach(function (tab) {

        tab.addEventListener('click', function () {

            const target = tab.getAttribute('data-review-tab');

            tabs.forEach(function (item) {
                item.classList.remove('active');
            });

            tabContents.forEach(function (content) {
                content.classList.remove('active');
            });

            tab.classList.add('active');

            const targetContent = document.querySelector(
                '[data-review-content="' + target + '"]'
            );

            if (targetContent) {
                targetContent.classList.add('active');
            }

        });

    });



    /* =========================================================
       POPUP ĐÁNH GIÁ
    ========================================================= */

    const dialog = document.getElementById('reviewDialog');

    if (!dialog) {
        return;
    }


    const openButtons =
        document.querySelectorAll('[data-review-open]');

    const closeButtons =
        document.querySelectorAll('[data-review-close]');


    const imgEl =
        document.getElementById('reviewProductImage');

    const nameEl =
        document.getElementById('reviewProductName');

    const sizeEl =
        document.getElementById('reviewProductSize');

    const colorEl =
        document.getElementById('reviewProductColor');


    // ID sản phẩm
    const productIdEl =
        document.getElementById('reviewProductId');

    // ID đơn hàng
    const orderIdEl =
        document.getElementById('reviewOrderId');



    /* =========================================================
       MỞ POPUP
    ========================================================= */

    openButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            const image =
                button.getAttribute('data-product-image');

            const productName =
                button.getAttribute('data-product-name') || '';

            const productSize =
                button.getAttribute('data-product-size') || '-';

            const productColor =
                button.getAttribute('data-product-color') || '';

            const productId =
                button.getAttribute('data-product-id') || '';

            const orderId =
                button.getAttribute('data-order-id') || '';


            /* ========================================
               THÔNG TIN SẢN PHẨM
            ======================================== */

            if (nameEl) {
                nameEl.textContent = productName;
            }

            if (sizeEl) {
                sizeEl.textContent = productSize;
            }

            if (colorEl) {
                colorEl.textContent =
                    productColor || '-';
            }


            /* ========================================
               ID GỬI CONTROLLER
            ======================================== */

            if (productIdEl) {
                productIdEl.value = productId;
            }

            if (orderIdEl) {
                orderIdEl.value = orderId;
            }


            /* ========================================
               ẢNH SẢN PHẨM
            ======================================== */

            if (imgEl) {

                if (image) {

                    imgEl.src = image;
                    imgEl.style.display = '';

                } else {

                    imgEl.removeAttribute('src');
                    imgEl.style.display = 'none';

                }

            }


            /* ========================================
               RESET FORM
            ======================================== */

            resetForm();


            /* ========================================
               MỞ POPUP
            ======================================== */

            if (!dialog.open) {
                dialog.showModal();
            }

        });

    });



    /* =========================================================
       TỰ ĐỘNG MỞ LẠI POPUP KHI VALIDATE LỖI
    ========================================================= */

    const validationError =
        dialog.getAttribute('data-validation-error');

    const oldProductId =
        dialog.getAttribute('data-old-product-id');

    const oldOrderId =
        dialog.getAttribute('data-old-order-id');


    if (
        validationError === '1' &&
        oldProductId &&
        oldOrderId
    ) {

        const errorButton = document.querySelector(
            '[data-review-open][data-product-id="' +
            oldProductId +
            '"][data-order-id="' +
            oldOrderId +
            '"]'
        );


        if (errorButton) {
            errorButton.click();
        }

    }



    /* =========================================================
       ĐÓNG POPUP
    ========================================================= */

    closeButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            dialog.close();

        });

    });



    /* =========================================================
       BẤM RA NGOÀI POPUP ĐỂ ĐÓNG
    ========================================================= */

    dialog.addEventListener('click', function (event) {

        if (event.target !== dialog) {
            return;
        }


        const rect =
            dialog.getBoundingClientRect();


        const clickedOutside =
            event.clientX < rect.left ||
            event.clientX > rect.right ||
            event.clientY < rect.top ||
            event.clientY > rect.bottom;


        if (clickedOutside) {
            dialog.close();
        }

    });



    /* =========================================================
       CHỌN SAO
    ========================================================= */

    const starButtons =
        document.querySelectorAll('.review-star');

    const ratingInput =
        document.getElementById('reviewRating');


    starButtons.forEach(function (star) {

        star.addEventListener('click', function () {

            const value =
                parseInt(
                    star.getAttribute('data-star'),
                    10
                );


            setStars(value);


            if (ratingInput) {
                ratingInput.value = value;
            }

        });

    });



    function setStars(value) {

        starButtons.forEach(function (star) {

            const starValue =
                parseInt(
                    star.getAttribute('data-star'),
                    10
                );


            star.classList.toggle(
                'active',
                starValue <= value
            );

        });

    }



    /* =========================================================
       ĐẾM KÝ TỰ BÌNH LUẬN
    ========================================================= */

    const comment =
        document.getElementById('reviewComment');

    const charCount =
        document.getElementById('reviewCharCount');

    const reviewForm =
        document.getElementById('reviewForm');

    const formError =
        document.getElementById('reviewFormError');


    function showFormError(message) {

        if (!formError) {
            return;
        }

        formError.textContent = message;
        formError.hidden = false;

    }


    function clearFormError() {

        if (!formError) {
            return;
        }

        formError.textContent = '';
        formError.hidden = true;

    }


    if (reviewForm) {

        reviewForm.addEventListener('submit', function (event) {

            if (comment && !comment.value.trim()) {
                event.preventDefault();
                showFormError('Vui lòng nhập bình luận.');
                comment.focus();
            }

        });

    }


    if (comment && charCount) {

        charCount.textContent = comment.value.length;

        comment.addEventListener(
            'input',
            function () {

                charCount.textContent =
                    comment.value.length;

                if (comment.value.trim()) {
                    clearFormError();
                }

            }
        );

    }



    /* =========================================================
       RESET FORM
    ========================================================= */

    function resetForm() {

        // Bỏ chọn sao
        setStars(0);


        // Xóa số sao
        if (ratingInput) {
            ratingInput.value = '';
        }


        // Xóa bình luận
        if (comment) {
            comment.value = '';
        }


        // Reset bộ đếm
        if (charCount) {
            charCount.textContent = '0';
        }

    }

});