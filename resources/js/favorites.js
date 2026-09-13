document.addEventListener('DOMContentLoaded', function () {

    const favoriteButtons = document.querySelectorAll('.product-favorite');

    favoriteButtons.forEach(function (button) {

        button.addEventListener('click', async function () {

            // Chặn double-click khi đang có request xử lý dở
            if (button.disabled) {
                return;
            }

            const productId = button.dataset.productId;
            const wishlistUrl = button.dataset.wishlistUrl;
            const csrfToken = button.dataset.csrfToken;

            if (!productId || !wishlistUrl || !csrfToken) {
                return;
            }

            // Khóa nút trong lúc chờ phản hồi
            button.disabled = true;

            try {

                const response = await fetch(wishlistUrl, {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },

                    body: JSON.stringify({
                        SanPhamID: productId
                    })
                });

                const data = await response.json();

                // =========================
                // CHƯA ĐĂNG NHẬP
                // =========================

                if (response.status === 401) {
                    showWishlistNotification(data.message);

                    setTimeout(function () {
                        window.location.href = '/dang-nhap';
                    }, 1500);

                    return;
                }

                // =========================
                // THÀNH CÔNG
                // =========================

                if (data.success) {

                    const icon = button.querySelector('i');

                    button.classList.toggle(
                        'active',
                        data.favorite
                    );

                    if (icon) {
                        icon.classList.toggle('fa-solid', data.favorite);
                        icon.classList.toggle('fa-regular', !data.favorite);
                    }

                    button.setAttribute(
                        'aria-pressed',
                        data.favorite ? 'true' : 'false'
                    );

                    button.setAttribute(
                        'title',
                        data.favorite
                            ? 'Bỏ khỏi yêu thích'
                            : 'Thêm vào yêu thích'
                    );

                    showWishlistNotification(data.message);
                    return;
                }

                // =========================
                // LỖI KHÁC (404, 422, 500...)
                // =========================

                showWishlistNotification(
                    data.message || 'Có lỗi xảy ra, vui lòng thử lại.'
                );

            } catch (error) {

                console.error('Lỗi wishlist:', error);

                showWishlistNotification(
                    'Không thể kết nối, vui lòng thử lại.'
                );

            } finally {

                // Mở khóa nút dù thành công hay thất bại
                button.disabled = false;
            }

        });

    });


    // =========================
    // THÔNG BÁO
    // =========================

    function showWishlistNotification(message) {

        if (!message) {
            return;
        }

        let notification =
            document.querySelector('.wishlist-notification');

        if (!notification) {

            notification = document.createElement('div');

            notification.className =
                'wishlist-notification';

            document.body.appendChild(notification);
        }

        notification.textContent = message;

        notification.classList.add('show');

        setTimeout(function () {
            notification.classList.remove('show');
        }, 2000);
    }

});