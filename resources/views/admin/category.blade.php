@extends('layouts.admin')

@section('content')
@vite([
    'resources/css/admin/category.css',
    'resources/js/admin/category.js'
])

<div class="category-page">

    {{-- Tiêu đề và nút thêm --}}
    <div class="category-heading">
        <h2>QUẢN LÝ DANH MỤC</h2>

        <button
            type="button"
            class="category-btn category-btn-primary"
            onclick="openCategoryModal('create')"
        >
            Thêm danh mục +
        </button>
    </div>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="category-alert category-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="category-alert category-alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="category-alert category-alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Tìm kiếm --}}
    <form
        action="{{ route('admin.category.index') }}"
        method="GET"
        class="category-search"
    >
        <input
            type="text"
            name="search"
            value="{{ $keyword ?? '' }}"
            placeholder="Tìm theo mã hoặc tên danh mục..."
            aria-label="Tìm kiếm danh mục"
        >

        <button type="submit" aria-label="Tìm kiếm">
            <span class="category-search-icon"></span>
        </button>
    </form>

    {{-- Bảng danh mục --}}
    <div class="category-table-wrap">
        <table class="category-table">
            <thead>
                <tr>
                    <th>Mã danh mục</th>
                    <th>Tên danh mục</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->DanhMucID }}</td>

                        <td>{{ $category->TenDanhMuc }}</td>

                        <td class="category-actions">
                            <button
                                type="button"
                                class="category-btn category-btn-edit"
                                data-category-action="edit"
                                data-category-id="{{ $category->DanhMucID }}"
                                data-category-name="{{ $category->TenDanhMuc }}"
                            >
                                Sửa
                            </button>

                            <button
                                type="button"
                                class="category-btn category-btn-delete"
                                data-category-action="delete"
                                data-category-id="{{ $category->DanhMucID }}"
                                data-category-name="{{ $category->TenDanhMuc }}"
                            >
                                Xóa
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="category-empty">
                            @if (!empty($keyword))
                                Không tìm thấy danh mục phù hợp.
                            @else
                                Chưa có danh mục nào.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Popup thêm / sửa danh mục --}}
<div
    class="category-modal-overlay"
    id="categoryModal"
    aria-hidden="true"
>
    <div
        class="category-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="categoryModalTitle"
    >
        <button
            type="button"
            class="category-modal-close"
            onclick="closeCategoryModal()"
            aria-label="Đóng"
        >
            ×
        </button>

        <h3 id="categoryModalTitle">THÊM DANH MỤC</h3>

        <form
            id="categoryForm"
            action="{{ route('admin.category.store') }}"
            method="POST"
        >
            @csrf

            <div id="categoryMethodField"></div>

            <label for="categoryName">Tên danh mục</label>

            <input
                type="text"
                id="categoryName"
                name="TenDanhMuc"
                maxlength="100"
                value="{{ old('TenDanhMuc') }}"
                placeholder="Nhập tên danh mục..."
                required
            >

            <div class="category-modal-actions">
                <button
                    type="button"
                    class="category-btn category-btn-cancel"
                    onclick="closeCategoryModal()"
                >
                    Hủy
                </button>

                <button
                    type="submit"
                    class="category-btn category-btn-save"
                >
                    Lưu danh mục
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Popup xác nhận xóa --}}
<div
    class="category-modal-overlay"
    id="deleteModal"
    aria-hidden="true"
>
    <div
        class="category-modal category-delete-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="deleteModalTitle"
    >
        <button
            type="button"
            class="category-modal-close"
            onclick="closeDeleteModal()"
            aria-label="Đóng"
        >
            ×
        </button>

        <h3 id="deleteModalTitle">XÁC NHẬN XÓA</h3>

        <p>
            Bạn có chắc muốn xóa danh mục
            <strong id="deleteCategoryName"></strong>
            không?
        </p>

        <form id="deleteCategoryForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="category-modal-actions">
                <button
                    type="button"
                    class="category-btn category-btn-cancel"
                    onclick="closeDeleteModal()"
                >
                    Hủy
                </button>

                <button
                    type="submit"
                    class="category-btn category-btn-delete"
                >
                    Xác nhận xóa
                </button>
            </div>
        </form>
    </div>
</div>


{{-- Dữ liệu cho JavaScript --}}
<div
    id="categoryPageConfig"
    data-config="{{ json_encode([
        'storeUrl' => route('admin.category.store'),
        'updateUrl' => route('admin.category.update', ['id' => '__ID__']),
        'deleteUrl' => route('admin.category.destroy', ['id' => '__ID__']),
        'hasValidationErrors' => $errors->any(),
        'oldInput' => old('TenDanhMuc'),
        'oldMode' => old('form_mode'),
        'oldId' => old('form_id'),
    ]) }}"
></div>

@endsection
