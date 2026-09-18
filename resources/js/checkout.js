document.addEventListener('DOMContentLoaded', function () {

    const addressRadios = document.querySelectorAll(
        'input[name="DiaChiNguoiDungID"]'
    );

    const shippingFeeElement = document.getElementById('shippingFee');

    const subtotal = Number(
        document.querySelector('.checkout-page')?.dataset.subtotal || 0
    );

    const totalElement = document.querySelector('.checkout-grand-total strong');

    let currentDiscount = 0;
    let currentShippingFee = 35000;


    function getShippingFeeFromSelectedAddress() {

        const selectedAddress = document.querySelector(
            'input[name="DiaChiNguoiDungID"]:checked'
        );

        let shippingFee = 35000;

        if (selectedAddress) {

            const city = (selectedAddress.dataset.city || '').trim();

            const normalizedCity = city
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]/g, '');

            if (normalizedCity === 'thanhphohochiminh' || normalizedCity === 'tphochiminh') {
                shippingFee = 0;
            }
        }

        return shippingFee;
    }


    function renderTotal() {

        currentShippingFee = getShippingFeeFromSelectedAddress();

        if (shippingFeeElement) {
            shippingFeeElement.textContent =
                currentShippingFee.toLocaleString('vi-VN') + ' VND';
        }

        const total = subtotal + currentShippingFee - currentDiscount;

        if (totalElement) {
            totalElement.textContent = total.toLocaleString('vi-VN') + ' VND';
        }
    }


    addressRadios.forEach(function (radio) {
        radio.addEventListener('change', renderTotal);
    });


    // =========================================================
    // MÃ GIẢM GIÁ
    // =========================================================

    const couponInput = document.querySelector('.checkout-coupon-input');
    const couponBtn = document.querySelector('.checkout-coupon-btn');
    const toast = document.querySelector('.cart-toast');
    let toastTimer;

    function showToast(message, type = 'success') {
        if (!toast) {
            return;
        }

        toast.textContent = message;
        toast.classList.remove('error');
        if (type === 'error') {
            toast.classList.add('error');
        }
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function () {
            toast.classList.remove('show');
        }, 3000);
    }

    if (couponBtn) {

        couponBtn.addEventListener('click', function () {

            if (couponBtn.dataset.applied === 'true') {
                currentDiscount = 0;
                document.getElementById('inputMaGiamGiaID').value = '';
                document.getElementById('inputMaCode').value = '';

                document.getElementById('discountRow')?.remove();

                couponBtn.dataset.applied = 'false';
                couponBtn.textContent = 'Áp dụng';
                couponInput.disabled = false;
                couponInput.value = '';
                renderTotal();
                couponInput.focus();
                showToast('Đã hủy mã giảm giá.');
                return;
            }

            const maCode = couponInput.value.trim();

            if (!maCode) {
                showToast('Vui lòng nhập mã khuyến mãi.', 'error');
                return;
            }

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content');

            fetch('/thanh-toan/ap-dung-ma', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    MaCode: maCode,
                    Subtotal: subtotal,
                }),
            })
            .then(res => res.json())
            .then(data => {

                if (!data.success) {
                    showToast(data.message, 'error');
                    return;
                }

                document.getElementById('inputMaGiamGiaID').value = data.MaGiamGiaID;
                document.getElementById('inputMaCode').value = data.MaCode;

                currentDiscount = data.SoTienGiam;

                let discountRow = document.getElementById('discountRow');

                if (!discountRow) {
                    discountRow = document.createElement('div');
                    discountRow.className = 'checkout-total-row';
                    discountRow.id = 'discountRow';
                    discountRow.innerHTML =
                        '<span id="discountLabel"></span><strong id="discountAmount"></strong>';

                    document.querySelector('.checkout-grand-total')
                        .insertAdjacentElement('beforebegin', discountRow);
                }

                document.getElementById('discountLabel').textContent =
                    data.MaCode + ' - Giảm ' + Number(data.GiaTriGiam) + '%:';

                document.getElementById('discountAmount').textContent =
                    '-' + currentDiscount.toLocaleString('vi-VN') + ' VND';

                couponBtn.dataset.applied = 'true';
                couponBtn.textContent = 'Hủy áp dụng';
                couponInput.disabled = true;

                renderTotal();
                showToast(data.message);
            })
            .catch(() => {
                showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
            });
        });
    }


    renderTotal();

});