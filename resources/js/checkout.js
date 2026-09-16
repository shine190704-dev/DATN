document.addEventListener('DOMContentLoaded', function () {

    const addressRadios = document.querySelectorAll(
        'input[name="DiaChiNguoiDungID"]'
    );

    const shippingFeeElement =
        document.getElementById('shippingFee');

    const subtotal = Number(
        document.querySelector('.checkout-page')?.dataset.subtotal || 0
    );

    const totalElement =
        document.querySelector(
            '.checkout-grand-total strong'
        );


    function updateShippingFee() {

        const selectedAddress =
            document.querySelector(
                'input[name="DiaChiNguoiDungID"]:checked'
            );

        let shippingFee = 35000;


        if (selectedAddress) {

            const city =
                (selectedAddress.dataset.city || '').trim();

            const normalizedCity = city
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]/g, '');

            if (normalizedCity === 'thanhphohochiminh' || normalizedCity === 'tphochiminh') {
                shippingFee = 0;
            }
        }


        // Cập nhật phí vận chuyển
        if (shippingFeeElement) {

            shippingFeeElement.textContent =
                shippingFee.toLocaleString('vi-VN') +
                ' VND';

        }


        // Cập nhật tổng cộng
        if (totalElement) {

            const total =
                subtotal + shippingFee;

            totalElement.textContent =
                total.toLocaleString('vi-VN') +
                ' VND';

        }

    }


    // Khi chọn địa chỉ khác
    addressRadios.forEach(function (radio) {

        radio.addEventListener(
            'change',
            updateShippingFee
        );

    });


    // Chạy khi trang vừa load
    updateShippingFee();

});