const cfg = window.categoryConfig || {};
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
    modal.classList.toggle('is-open', open);
    modal.setAttribute('aria-hidden', String(!open));
}

function openCategoryModal(mode, id = null, name = '') {
    const isEdit = mode === 'edit' && id !== null && id !== '';

    lastFocused = document.activeElement;

    categoryForm.action = isEdit ? buildUrl(cfg.updateUrl, id) : cfg.storeUrl;
    categoryTitle.textContent = isEdit ? 'SỬA DANH MỤC' : 'THÊM DANH MỤC';

    // Nhớ chế độ để khi validate thất bại, popup mở lại đúng chế độ.
    categoryMethodField.innerHTML =
        `<input type="hidden" name="form_mode" value="${isEdit ? 'edit' : 'create'}">` +
        `<input type="hidden" name="form_id" value="${isEdit ? id : ''}">` +
        (isEdit ? '<input type="hidden" name="_method" value="PUT">' : '');

    categoryName.value = name || '';

    setOpen(categoryModal, true);
    setTimeout(() => categoryName.focus(), 50);
}

function closeCategoryModal() {
    setOpen(categoryModal, false);
    lastFocused?.focus?.();
}

function openDeleteModal(id, name) {
    lastFocused = document.activeElement;

    deleteCategoryName.textContent = name;
    deleteForm.action = buildUrl(cfg.deleteUrl || cfg.updateUrl, id);

    setOpen(deleteModal, true);
}

function closeDeleteModal() {
    setOpen(deleteModal, false);
    lastFocused?.focus?.();
}

// Các nút trong Blade gọi bằng onclick nên phải đưa ra window.
Object.assign(window, {
    openCategoryModal,
    closeCategoryModal,
    openDeleteModal,
    closeDeleteModal,
});

// Bấm ra nền để đóng
document.querySelectorAll('.category-modal-overlay').forEach((modal) => {
    modal.addEventListener('click', (event) => {
        if (event.target === modal) setOpen(modal, false);
    });
});

// Esc đóng popup đang mở
document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    if (categoryModal.classList.contains('is-open')) closeCategoryModal();
    if (deleteModal.classList.contains('is-open')) closeDeleteModal();
});

// Chống bấm hai lần
[categoryForm, deleteForm].forEach((form) => {
    form.addEventListener('submit', () => {
        form.querySelector('[type="submit"]').disabled = true;
    });
});
window.addEventListener('pageshow', () => {
    document.querySelectorAll('.category-modal [type="submit"]').forEach((b) => (b.disabled = false));
});

// Mở lại popup khi validate thất bại
if (cfg.hasValidationErrors && cfg.oldMode) {
    openCategoryModal(cfg.oldMode, cfg.oldId || null, cfg.oldInput || '');
}