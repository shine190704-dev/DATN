const cfg = (() => {
    const configNode = document.getElementById('categoryPageConfig');
    const raw = configNode?.dataset?.config;

    if (!raw) {
        return window.categoryConfig || {};
    }

    try {
        return JSON.parse(raw);
    } catch (error) {
        console.warn('Category config parse failed:', error);
        return window.categoryConfig || {};
    }
})();

const $ = (id) => document.getElementById(id);

const categoryModal = $('categoryModal');
const categoryForm = $('categoryForm');
const categoryTitle = $('categoryModalTitle');
const categoryName = $('categoryName');
const categoryMethodField = $('categoryMethodField');

const deleteModal = $('deleteModal');
const deleteForm = $('deleteCategoryForm');
const deleteCategoryName = $('deleteCategoryName');

let lastFocused = null;

function buildUrl(template, id) {
    return String(template).replace('__ID__', encodeURIComponent(id));
}

function setOpen(modal, open) {
    if (!modal) return;
    modal.classList.toggle('is-open', open);
    modal.setAttribute('aria-hidden', String(!open));
}

function openCategoryModal(mode, id = null, name = '') {
    if (!categoryModal || !categoryForm || !categoryTitle || !categoryName || !categoryMethodField) {
        return;
    }

    const isEdit = mode === 'edit' && id !== null && id !== '';

    lastFocused = document.activeElement;

    categoryForm.action = isEdit ? buildUrl(cfg.updateUrl, id) : cfg.storeUrl;
    categoryTitle.textContent = isEdit ? 'SỬA DANH MỤC' : 'THÊM DANH MỤC';

    categoryMethodField.innerHTML =
        `<input type="hidden" name="form_mode" value="${isEdit ? 'edit' : 'create'}">` +
        `<input type="hidden" name="form_id" value="${isEdit ? id : ''}">` +
        (isEdit ? '<input type="hidden" name="_method" value="PUT">' : '');

    categoryName.value = name || '';

    setOpen(categoryModal, true);
    setTimeout(() => categoryName?.focus?.(), 50);
}

function closeCategoryModal() {
    setOpen(categoryModal, false);
    lastFocused?.focus?.();
}

function openDeleteModal(id, name) {
    if (!deleteModal || !deleteForm || !deleteCategoryName) {
        return;
    }

    lastFocused = document.activeElement;

    deleteCategoryName.textContent = name;
    deleteForm.action = buildUrl(cfg.deleteUrl || cfg.updateUrl, id);

    setOpen(deleteModal, true);
}

function closeDeleteModal() {
    setOpen(deleteModal, false);
    lastFocused?.focus?.();
}

function initCategoryModalHandlers() {
    if (!categoryModal && !deleteModal && !categoryForm && !deleteForm) {
        return;
    }

    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-category-action]');
        if (!button) return;

        const { categoryAction, categoryId, categoryName: name } = button.dataset;
        if (categoryAction === 'edit') {
            openCategoryModal('edit', categoryId, name || '');
        } else if (categoryAction === 'delete') {
            openDeleteModal(categoryId, name || '');
        }
    });

    document.querySelectorAll('.category-modal-overlay').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) setOpen(modal, false);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        if (categoryModal?.classList.contains('is-open')) closeCategoryModal();
        if (deleteModal?.classList.contains('is-open')) closeDeleteModal();
    });

    if (categoryForm) {
        categoryForm.addEventListener('submit', () => {
            const submitButton = categoryForm.querySelector('[type="submit"]');
            if (submitButton) submitButton.disabled = true;
        });
    }

    if (deleteForm) {
        deleteForm.addEventListener('submit', () => {
            const submitButton = deleteForm.querySelector('[type="submit"]');
            if (submitButton) submitButton.disabled = true;
        });
    }

    window.addEventListener('pageshow', () => {
        document.querySelectorAll('.category-modal [type="submit"]').forEach((button) => {
            button.disabled = false;
        });
    });

    if (cfg.hasValidationErrors && cfg.oldMode) {
        openCategoryModal(cfg.oldMode, cfg.oldId || null, cfg.oldInput || '');
    }
}

Object.assign(window, {
    openCategoryModal,
    closeCategoryModal,
    openDeleteModal,
    closeDeleteModal,
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCategoryModalHandlers, { once: true });
} else {
    initCategoryModalHandlers();
}