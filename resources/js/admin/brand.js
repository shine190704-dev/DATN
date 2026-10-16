
const cfg = (() => {
    const configNode = document.getElementById('brandPageConfig');
    const raw = configNode?.dataset?.config;

    if (!raw) {
        return window.brandConfig || {};
    }

    try {
        return JSON.parse(raw);
    } catch (error) {
        console.warn('Brand config parse failed:', error);
        return window.brandConfig || {};
    }
})();

const $ = (id) => document.getElementById(id);

const brandModal = $('brandModal');
const brandForm = $('brandForm');
const brandTitle = $('brandModalTitle');
const brandName = $('brandName');
const brandMethodField = $('brandMethodField');

const brandDeleteModal = $('brandDeleteModal');
const brandDeleteForm = $('brandDeleteForm');
const brandDeleteName = $('brandDeleteName');

let lastFocused = null;

function buildUrl(template, id) {
    return String(template).replace('__ID__', encodeURIComponent(id));
}

function setOpen(modal, open) {
    if (!modal) return;

    modal.classList.toggle('is-open', open);
    modal.setAttribute('aria-hidden', String(!open));
}

function openBrandModal(mode, id = null, name = '') {
    if (
        !brandModal ||
        !brandForm ||
        !brandTitle ||
        !brandName ||
        !brandMethodField
    ) {
        return;
    }

    const isEdit = mode === 'edit' && id !== null && id !== '';

    lastFocused = document.activeElement;

    brandForm.action = isEdit
        ? buildUrl(cfg.updateUrl, id)
        : cfg.storeUrl;

    brandTitle.textContent = isEdit
        ? 'SỬA THƯƠNG HIỆU'
        : 'THÊM THƯƠNG HIỆU';

    brandMethodField.innerHTML =
        `<input type="hidden" name="form_mode" value="${isEdit ? 'edit' : 'create'}">` +
        `<input type="hidden" name="form_id" value="${isEdit ? id : ''}">` +
        (isEdit ? '<input type="hidden" name="_method" value="PUT">' : '');

    brandName.value = name || '';

    setOpen(brandModal, true);

    setTimeout(() => brandName.focus(), 50);
}

function closeBrandModal() {
    setOpen(brandModal, false);
    lastFocused?.focus?.();
}

function openBrandDeleteModal(id, name) {
    if (!brandDeleteModal || !brandDeleteForm || !brandDeleteName) {
        return;
    }

    lastFocused = document.activeElement;

    brandDeleteName.textContent = name || '';
    brandDeleteForm.action = buildUrl(cfg.deleteUrl, id);

    setOpen(brandDeleteModal, true);
}

function closeBrandDeleteModal() {
    setOpen(brandDeleteModal, false);
    lastFocused?.focus?.();
}

function initBrandHandlers() {
    if (!brandModal && !brandDeleteModal) return;

    // Bấm Sửa hoặc Xóa trong bảng.
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-brand-action]');

        if (!button) return;

        const { brandAction, brandId, brandName: name } = button.dataset;

        if (brandAction === 'edit') {
            openBrandModal('edit', brandId, name || '');
        }

        if (brandAction === 'delete') {
            openBrandDeleteModal(brandId, name || '');
        }
    });

    // Bấm ra ngoài popup để đóng.
    document.querySelectorAll(
        '#brandModal, #brandDeleteModal'
    ).forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                setOpen(modal, false);
            }
        });
    });

    // Nhấn Escape để đóng popup.
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        if (brandModal?.classList.contains('is-open')) {
            closeBrandModal();
        }

        if (brandDeleteModal?.classList.contains('is-open')) {
            closeBrandDeleteModal();
        }
    });

    // Tránh gửi form nhiều lần.
    [brandForm, brandDeleteForm].forEach((form) => {
        if (!form) return;

        form.addEventListener('submit', () => {
            const button = form.querySelector('[type="submit"]');

            if (button) {
                button.disabled = true;
            }
        });
    });

    // Mở lại popup nếu Laravel trả lỗi validation.
    if (cfg.hasValidationErrors && cfg.oldMode) {
        openBrandModal(cfg.oldMode, cfg.oldId || null, cfg.oldInput || '');
    }
}

Object.assign(window, {
    openBrandModal,
    closeBrandModal,
    openBrandDeleteModal,
    closeBrandDeleteModal
});

if (document.readyState === 'loading') {
    document.addEventListener(
        'DOMContentLoaded',
        initBrandHandlers,
        { once: true }
    );
} else {
    initBrandHandlers();
}
