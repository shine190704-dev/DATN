
@extends('layouts.admin')

@section('content')
@vite([
    'resources/css/admin/category.css',
    'resources/js/admin/brand.js'
])

<div class="category-page">

    <div class="category-heading">
        <h2>QUẢN LÝ THƯƠNG HIỆU</h2>

        <button
            type="button"
            class="category-btn category-btn-primary"
            onclick="openBrandModal('create')"
        >
            Thêm thương hiệu +
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
        action="{{ route('admin.brand.index') }}"
        method="GET"
        class="category-search"
    >
        <input
            type="text"
            name="search"
            value="{{ $keyword ?? '' }}"
            placeholder="Tìm theo mã hoặc tên thương hiệu..."
            aria-label="Tìm kiếm thương hiệu"
        >

        <button type="submit" aria-label="Tìm kiếm">
            <span class="category-search-icon"></span>
        </button>
    </form>

    {{-- Bảng thương hiệu --}}
    <div class="category-table-wrap">
        <table class="category-table">
            <thead>
                <tr>
                    <th>Mã thương hiệu</th>
                    <th>Tên thương hiệu</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($brands as $brand)
                    <tr>
                        <td>{{ $brand->ThuongHieuID }}</td>
                        <td>{{ $brand->TenThuongHieu }}</td>

                        <td class="category-actions">
                            <button
                                type="button"
                                class="category-btn category-btn-edit"
                                data-brand-action="edit"
                                data-brand-id="{{ $brand->ThuongHieuID }}"
                                data-brand-name="{{ $brand->TenThuongHieu }}"
                            >
                                Sửa
                            </button>

                            <button
                                type="button"
                                class="category-btn category-btn-delete"
                                data-brand-action="delete"
                                data-brand-id="{{ $brand->ThuongHieuID }}"
                                data-brand-name="{{ $brand->TenThuongHieu }}"
                            >
                                Xóa
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="category-empty">
                            @if (!empty($keyword))
                                Không tìm thấy thương hiệu phù hợp.
                            @else
                                Chưa có thương hiệu nào.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Popup thêm / sửa thương hiệu --}}
<div
    class="category-modal-overlay"
    id="brandModal"
    aria-hidden="true"
>
    <div
        class="category-modal brand-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="brandModalTitle"
    >
        <button
            type="button"
            class="category-modal-close"
            onclick="closeBrandModal()"
            aria-label="Đóng"
        >
            ×
        </button>

        <h3 id="brandModalTitle">THÊM THƯƠNG HIỆU</h3>

        <form
            id="brandForm"
            action="{{ route('admin.brand.store') }}"
            method="POST"
        >
            @csrf

            <div id="brandMethodField"></div>

            <label for="brandName">Tên thương hiệu</label>
            <input
                type="text"
                id="brandName"
                name="TenThuongHieu"
                maxlength="150"
                value="{{ old('TenThuongHieu') }}"
                placeholder="Nhập tên thương hiệu..."
                required
            >

            <div class="category-modal-actions">
                <button
                    type="button"
                    class="category-btn category-btn-cancel"
                    onclick="closeBrandModal()"
                >
                    Hủy
                </button>

                <button
                    type="submit"
                    class="category-btn category-btn-save"
                >
                    Lưu thương hiệu
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Popup xác nhận xóa --}}
<div
    class="category-modal-overlay"
    id="brandDeleteModal"
    aria-hidden="true"
>
    <div
        class="category-modal category-delete-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="brandDeleteTitle"
    >
        <button
            type="button"
            class="category-modal-close"
            onclick="closeBrandDeleteModal()"
            aria-label="Đóng"
        >
            ×
        </button>

        <h3 id="brandDeleteTitle">XÁC NHẬN XÓA</h3>

        <p>
            Bạn có chắc muốn xóa thương hiệu
            <strong id="brandDeleteName"></strong>
            không?
        </p>

        <form id="brandDeleteForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="category-modal-actions">
                <button
                    type="button"
                    class="category-btn category-btn-cancel"
                    onclick="closeBrandDeleteModal()"
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

{{-- Cấu hình JavaScript --}}
<div
    id="brandPageConfig"
    data-config="{{ json_encode([
        'storeUrl' => route('admin.brand.store'),
        'updateUrl' => route('admin.brand.update', ['id' => '__ID__']),
        'deleteUrl' => route('admin.brand.destroy', ['id' => '__ID__']),
        'hasValidationErrors' => $errors->any(),
        'oldInput' => old('TenThuongHieu'),
        'oldMode' => old('form_mode'),
        'oldId' => old('form_id'),
    ]) }}"
></div>

@endsection
