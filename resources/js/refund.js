// ========================================
// YÊU CẦU HOÀN TIỀN
// ========================================

(function () {

    const dialog = document.getElementById('refundDialog');

    if (!dialog) {
        return;
    }


    // ========================================
    // MỞ / ĐÓNG POPUP
    // ========================================

    document.querySelectorAll('[data-refund-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!dialog.open) {
                dialog.showModal();
            }
        });
    });

    dialog.querySelectorAll('[data-refund-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            dialog.close();
        });
    });

    // Bấm ra ngoài popup để đóng
    dialog.addEventListener('click', function (event) {

        if (event.target !== dialog) {
            return;
        }

        const rect = dialog.getBoundingClientRect();

        const clickedOutside =
            event.clientX < rect.left ||
            event.clientX > rect.right ||
            event.clientY < rect.top ||
            event.clientY > rect.bottom;

        if (clickedOutside) {
            dialog.close();
        }
    });


    // ========================================
    // TỰ ĐỘNG MỞ POPUP
    // (khi có lỗi nhập liệu hoặc đi từ trang Đơn hàng của tôi)
    // Giá trị lấy từ thuộc tính data-auto-open trên thẻ <dialog>
    // ========================================

    if (dialog.dataset.autoOpen === '1' && !dialog.open) {
        dialog.showModal();
    }


    // ========================================
    // CHỌN ẢNH MINH CHỨNG (TỐI ĐA 5 ẢNH)
    // ========================================

    const MAX_IMAGES = 5;

    const input = document.getElementById('refundImages');
    const addButton = document.getElementById('refundAddImage');
    const previewList = document.getElementById('refundPreview');

    let selectedFiles = [];

    if (input && addButton && previewList) {

        // Hiển thị ảnh xem trước
        const renderImages = function () {

            previewList.querySelectorAll('.refund-thumb').forEach(function (element) {
                element.remove();
            });

            selectedFiles.forEach(function (file, index) {

                const box = document.createElement('div');
                box.className = 'refund-thumb';

                const image = document.createElement('img');
                image.src = URL.createObjectURL(file);
                image.alt = file.name;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.textContent = '×';
                removeButton.setAttribute('aria-label', 'Xóa ảnh');

                removeButton.addEventListener('click', function () {
                    URL.revokeObjectURL(image.src);
                    selectedFiles.splice(index, 1);
                    syncFiles();
                });

                box.appendChild(image);
                box.appendChild(removeButton);

                // Đưa ảnh vào trước nút "+"
                previewList.insertBefore(box, addButton);
            });

            // Đủ 5 ảnh thì ẩn nút "+"
            addButton.hidden = selectedFiles.length >= MAX_IMAGES;
        };

        // Đồng bộ danh sách ảnh vào ô file để gửi lên server
        const syncFiles = function () {

            const dataTransfer = new DataTransfer();

            selectedFiles.forEach(function (file) {
                dataTransfer.items.add(file);
            });

            input.files = dataTransfer.files;

            renderImages();
        };

        addButton.addEventListener('click', function () {
            if (selectedFiles.length >= MAX_IMAGES) {
                return;
            }

            input.click();
        });

        input.addEventListener('change', function () {
            selectedFiles = selectedFiles
                .concat(Array.from(input.files))
                .slice(0, MAX_IMAGES);

            syncFiles();
        });
    }


    // ========================================
    // VIDEO MINH CHỨNG (MỘT VIDEO, KHÔNG BẮT BUỘC)
    // ========================================

    const MAX_VIDEO = 20 * 1024 * 1024;   // 20MB

    const videoInput = document.getElementById('refundVideo');
    const videoBtn = document.getElementById('refundAddVideo');
    const videoName = document.getElementById('refundVideoName');
    const videoRemove = document.getElementById('refundRemoveVideo');

    if (videoInput && videoBtn && videoName && videoRemove) {

        const resetVideo = function () {
            videoInput.value = '';
            videoName.textContent = '';
            videoName.hidden = true;
            videoRemove.hidden = true;
            videoBtn.textContent = 'Chọn video';
        };

        videoBtn.addEventListener('click', function () {
            videoInput.click();
        });

        videoInput.addEventListener('change', function () {

            const file = videoInput.files[0];

            if (!file) {
                resetVideo();
                return;
            }

            if (file.size > MAX_VIDEO) {
                alert('Video tối đa 20MB. Vui lòng chọn video nhỏ hơn.');
                resetVideo();
                return;
            }

            videoName.textContent = file.name;
            videoName.hidden = false;
            videoRemove.hidden = false;
            videoBtn.textContent = 'Đổi video';
        });

        videoRemove.addEventListener('click', resetVideo);
    }


    // ========================================
// HỦY YÊU CẦU HOÀN TIỀN
// ========================================

(function () {

    const cancelDialog = document.getElementById('refundCancelDialog');
    const cancelForm = document.getElementById('refundCancelForm');
    const cancelClose = document.getElementById('refundCancelClose');

    if (!cancelDialog || !cancelForm || !cancelClose) {
        return;
    }

    document.querySelectorAll('[data-refund-cancel-url]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            cancelForm.action = btn.dataset.refundCancelUrl;
            cancelDialog.showModal();
        });
    });

    cancelClose.addEventListener('click', function () {
        cancelDialog.close();
    });

    // Bấm ra ngoài hộp thì đóng
    cancelDialog.addEventListener('click', function (event) {

        if (event.target !== cancelDialog) {
            return;
        }

        const rect = cancelDialog.getBoundingClientRect();

        const outside =
            event.clientX < rect.left ||
            event.clientX > rect.right ||
            event.clientY < rect.top ||
            event.clientY > rect.bottom;

        if (outside) {
            cancelDialog.close();
        }
    });

})();

})();