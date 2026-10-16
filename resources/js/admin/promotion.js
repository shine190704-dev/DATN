document.addEventListener('DOMContentLoaded', function () {
    const configElement = document.getElementById('promotionPageConfig');

    let config = {};

    try {
        config = JSON.parse(configElement?.dataset.config || '{}');
    } catch (error) {
        console.error('Không đọc được cấu hình mã khuyến mãi:', error);
    }

    // Các phần tử popup
    const promotionModal = document.getElementById('promotionModal');
    const promotionDeleteModal = document.getElementById('promotionDeleteModal');

    const promotionForm = document.getElementById('promotionForm');
    const promotionDeleteForm = document.getElementById('promotionDeleteForm');

    const promotionMethodField = document.getElementById('promotionMethodField');

    const promotionModalTitle = document.getElementById('promotionModalTitle');
    const promotionDeleteCode = document.getElementById('promotionDeleteCode');

    // Các ô nhập liệu
    const promotionCode = document.getElementById('promotionCode');
    const promotionValue = document.getElementById('promotionValue');
    const promotionMinimum = document.getElementById('promotionMinimum');
    const promotionStart = document.getElementById('promotionStart');
    const promotionEnd = document.getElementById('promotionEnd');
    const promotionQuantity = document.getElementById('promotionQuantity');
    const promotionStatus = document.getElementById('promotionStatus');

    function formatDateForDisplay(value) {
        const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);

        return match ? `${match[3]}/${match[2]}/${match[1].slice(-2)}` : value;
    }

    function parseDisplayDate(value) {
        const match = String(value || '').match(/^(\d{2})\/(\d{2})\/(\d{2})$/);

        if (!match) return null;

        const day = Number(match[1]);
        const month = Number(match[2]);
        const year = 2000 + Number(match[3]);
        const date = new Date(Date.UTC(year, month - 1, day));

        if (
            date.getUTCFullYear() !== year
            || date.getUTCMonth() !== month - 1
            || date.getUTCDate() !== day
        ) {
            return null;
        }

        return `${year}-${match[2]}-${match[1]}`;
    }

    promotionStart.value = formatDateForDisplay(promotionStart.value);
    promotionEnd.value = formatDateForDisplay(promotionEnd.value);

    [promotionStart, promotionEnd].forEach(function (input) {
        input.addEventListener('input', function () {
            input.setCustomValidity('');
        });
    });

    promotionForm.addEventListener('submit', function (event) {
        const startDate = parseDisplayDate(promotionStart.value);
        const endDate = parseDisplayDate(promotionEnd.value);

        if (!startDate || !endDate) {
            event.preventDefault();
            const invalidInput = !startDate ? promotionStart : promotionEnd;

            invalidInput.setCustomValidity('Vui lòng nhập ngày hợp lệ theo định dạng dd/mm/yy.');
            invalidInput.reportValidity();
            return;
        }

        promotionStart.value = startDate;
        promotionEnd.value = endDate;
    });

    /**
     * Mở popup
     */
    function showModal(modal) {
        if (!modal) return;

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Đóng popup
     */
    function hideModal(modal) {
        if (!modal) return;

        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');

        const anotherModalIsOpen = document.querySelector(
            '.category-modal-overlay.is-open'
        );

        if (!anotherModalIsOpen) {
            document.body.style.overflow = '';
        }
    }

    /**
     * Thay ID vào URL cập nhật hoặc xóa
     */
    function buildUrl(template, id) {
        return String(template || '').replace('__ID__', encodeURIComponent(id));
    }

    /**
     * Thêm phương thức PUT vào form khi sửa
     */
    function setFormMethod(method) {
        promotionMethodField.innerHTML = '';

        if (method) {
            const input = document.createElement('input');

            input.type = 'hidden';
            input.name = '_method';
            input.value = method;

            promotionMethodField.appendChild(input);
        }

        // Gửi mode và ID để khôi phục popup khi validation lỗi
        const modeInput = document.createElement('input');
        modeInput.type = 'hidden';
        modeInput.name = 'form_mode';
        modeInput.value = method === 'PUT' ? 'edit' : 'create';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'form_id';
        idInput.value = method === 'PUT'
            ? promotionForm.dataset.promotionId || ''
            : '';

        promotionMethodField.appendChild(modeInput);
        promotionMethodField.appendChild(idInput);
    }

    /**
     * Mở popup tạo mã khuyến mãi
     */
    window.openPromotionModal = function (mode = 'create', data = null) {
        if (!promotionModal || !promotionForm) return;

        promotionForm.dataset.promotionId = '';

        if (mode === 'edit' && data) {
            promotionModalTitle.textContent = 'CẬP NHẬT MÃ KHUYẾN MÃI';

            promotionForm.action = buildUrl(config.updateUrl, data.id);

            promotionForm.dataset.promotionId = data.id;

            promotionCode.value = data.code || '';
            promotionValue.value = data.value || '';
            promotionMinimum.value = data.minimum || '0';
            promotionStart.value = formatDateForDisplay(data.start || '');
            promotionEnd.value = formatDateForDisplay(data.end || '');
            promotionQuantity.value = data.quantity || '1';
            promotionStatus.value = data.status || 'HoatDong';

            setFormMethod('PUT');
        } else {
            promotionModalTitle.textContent = 'TẠO MÃ KHUYẾN MÃI';

            promotionForm.action = config.storeUrl || '';

            promotionCode.value = '';
            promotionValue.value = '';
            promotionMinimum.value = '0';
            promotionStart.value = '';
            promotionEnd.value = '';
            promotionQuantity.value = '1';
            promotionStatus.value = 'HoatDong';

            setFormMethod('');
        }

        showModal(promotionModal);

        setTimeout(function () {
            promotionCode?.focus();
        }, 50);
    };

    /**
     * Đóng popup thêm / sửa
     */
    window.closePromotionModal = function () {
        hideModal(promotionModal);
    };

    /**
     * Mở popup xác nhận xóa
     */
    window.openPromotionDeleteModal = function (id, code) {
        if (!promotionDeleteModal || !promotionDeleteForm) return;

        promotionDeleteForm.action = buildUrl(config.deleteUrl, id);
        promotionDeleteCode.textContent = code || '';

        showModal(promotionDeleteModal);
    };

    /**
     * Đóng popup xác nhận xóa
     */
    window.closePromotionDeleteModal = function () {
        hideModal(promotionDeleteModal);
    };

    /**
     * Xử lý nút Sửa và Xóa
     */
    document.querySelectorAll('[data-promotion-action]').forEach(function (button) {
        button.addEventListener('click', function () {
            const action = button.dataset.promotionAction;
            const id = button.dataset.promotionId;
            const code = button.dataset.promotionCode;

            if (action === 'edit') {
                window.openPromotionModal('edit', {
                    id: id,
                    code: code,
                    value: button.dataset.promotionValue,
                    minimum: button.dataset.promotionMinimum,
                    start: button.dataset.promotionStart,
                    end: button.dataset.promotionEnd,
                    quantity: button.dataset.promotionQuantity,
                    status: button.dataset.promotionStatus
                });
            }

            if (action === 'delete') {
                window.openPromotionDeleteModal(id, code);
            }
        });
    });

    /**
     * Bấm ra ngoài popup để đóng
     */
    [promotionModal, promotionDeleteModal].forEach(function (modal) {
        modal?.addEventListener('click', function (event) {
            if (event.target === modal) {
                hideModal(modal);
            }
        });
    });

    /**
     * Nhấn ESC để đóng popup
     */
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            window.closePromotionModal();
            window.closePromotionDeleteModal();
        }
    });

    /**
     * Khôi phục popup nếu Laravel trả về lỗi validation
     */
    if (config.hasValidationErrors) {
        const oldMode = config.oldMode || 'create';
        const oldId = config.oldId || '';

        if (oldMode === 'edit' && oldId) {
            promotionForm.dataset.promotionId = oldId;
            promotionForm.action = buildUrl(config.updateUrl, oldId);
            promotionModalTitle.textContent = 'CẬP NHẬT MÃ KHUYẾN MÃI';

            setFormMethod('PUT');
        } else {
            promotionForm.action = config.storeUrl || '';
            promotionModalTitle.textContent = 'TẠO MÃ KHUYẾN MÃI';

            setFormMethod('');
        }

        showModal(promotionModal);
    }
});