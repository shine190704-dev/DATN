 @extends('layouts.admin')

@section('content')
@vite([
    'resources/css/admin/category.css',
    'resources/css/admin/promotion.css',
    'resources/js/admin/promotion.js'
])

<div class="category-page promotion-page">

    {{-- Tiêu đề và nút tạo mã --}}
    <div class="category-heading">
        <h2>QUẢN LÝ MÃ KHUYẾN MÃI</h2>

        <button
            type="button"
            class="category-btn category-btn-primary"
            onclick="openPromotionModal('create')"
        >
            Tạo mã khuyến mãi +
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

    {{-- Tìm kiếm mã khuyến mãi --}}
    <form
        action="{{ route('admin.promotion.index') }}"
        method="GET"
        class="category-search promotion-search"
    >
        <input
            type="text"
            name="search"
            value="{{ $keyword ?? request('search', '') }}"
            placeholder="Tìm theo mã khuyến mãi..."
            aria-label="Tìm kiếm mã khuyến mãi"
        >

        <button type="submit" aria-label="Tìm kiếm">
            <span class="category-search-icon"></span>
        </button>
    </form>

    {{-- Bảng mã khuyến mãi --}}
    <div class="category-table-wrap">
        <table class="category-table promotion-table">
            <thead>
                <tr>
                    <th>Mã khuyến mãi</th>
                    <th>Mức giảm</th>
                    <th>Đơn tối thiểu</th>
                    <th>Thời gian áp dụng</th>
                    <th>Lượt nhận</th>
                    <th>Đã dùng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($promotions as $promotion)
                    @php
                        $stats = $promotionStats->get($promotion->MaGiamGiaID);

                        $issued = (int) ($stats->LuotNhan ?? 0);
                        $used = (int) ($stats->LuotDaDung ?? 0);
                        $quantity = (int) $promotion->SoLuongPhatHanh;

                        $today = now()->toDateString();
                        $start = \Carbon\Carbon::parse($promotion->NgayBatDau)->startOfDay();
                        $end = \Carbon\Carbon::parse($promotion->NgayHetHan)->startOfDay();

                        if ($promotion->TrangThai !== 'HoatDong') {
                            $displayStatus = 'Tạm dừng';
                            $statusClass = 'promotion-status-paused';
                        } elseif ($today < $start->toDateString()) {
                            $displayStatus = 'Sắp diễn ra';
                            $statusClass = 'promotion-status-upcoming';
                        } elseif ($today > $end->toDateString()) {
                            $displayStatus = 'Hết hạn';
                            $statusClass = 'promotion-status-expired';
                        } elseif ($issued >= $quantity) {
                            $displayStatus = 'Hết lượt nhận';
                            $statusClass = 'promotion-status-exhausted';
                        } else {
                            $displayStatus = 'Đang hoạt động';
                            $statusClass = 'promotion-status-active';
                        }
                    @endphp

                    <tr>
                        <td>
                            <strong class="promotion-code">
                                {{ $promotion->MaCode }}
                            </strong>
                        </td>

                        <td>
                            <strong class="promotion-discount">
                                {{ rtrim(rtrim(number_format((float) $promotion->GiaTriGiam, 2, '.', ''), '0'), '.') }}%
                            </strong>
                        </td>

                        <td>
                            {{ number_format((float) $promotion->GiaTriDonHangToiThieu, 0, ',', '.') }}đ
                        </td>

                        <td class="promotion-date">
                            <div>
                                {{ $start->format('d/m/Y') }}
                            </div>
                            <span>đến</span>
                            <div>
                                {{ $end->format('d/m/Y') }}
                            </div>
                        </td>

                        <td>
                            {{ $issued }}/{{ $quantity }}
                        </td>

                        <td>{{ $used }}</td>

                        <td>
                            <span class="promotion-status {{ $statusClass }}">
                                {{ $displayStatus }}
                            </span>
                        </td>

                        <td class="category-actions">
                            <button
                                type="button"
                                class="category-btn category-btn-edit"
                                data-promotion-action="edit"
                                data-promotion-id="{{ $promotion->MaGiamGiaID }}"
                                data-promotion-code="{{ $promotion->MaCode }}"
                                data-promotion-value="{{ $promotion->GiaTriGiam }}"
                                data-promotion-minimum="{{ $promotion->GiaTriDonHangToiThieu }}"
                                data-promotion-start="{{ $start->format('Y-m-d') }}"
                                data-promotion-end="{{ $end->format('Y-m-d') }}"
                                data-promotion-quantity="{{ $promotion->SoLuongPhatHanh }}"
                                data-promotion-status="{{ $promotion->TrangThai }}"
                            >
                                Sửa
                            </button>

                            <button
                                type="button"
                                class="category-btn category-btn-delete"
                                data-promotion-action="delete"
                                data-promotion-id="{{ $promotion->MaGiamGiaID }}"
                                data-promotion-code="{{ $promotion->MaCode }}"
                            >
                                Xóa
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="category-empty">
                            @if (!empty($keyword))
                                Không tìm thấy mã khuyến mãi phù hợp.
                            @else
                                Chưa có mã khuyến mãi nào.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- Popup thêm / sửa mã khuyến mãi --}}
<div
    class="category-modal-overlay"
    id="promotionModal"
    aria-hidden="true"
>
    <div
        class="category-modal promotion-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="promotionModalTitle"
    >
        <button
            type="button"
            class="category-modal-close"
            onclick="closePromotionModal()"
            aria-label="Đóng"
        >
            &times;
        </button>

        <h3 id="promotionModalTitle">TẠO MÃ KHUYẾN MÃI</h3>

        <form
            id="promotionForm"
            action="{{ route('admin.promotion.store') }}"
            method="POST"
        >
            @csrf

            <div id="promotionMethodField"></div>

            <div class="promotion-form-group">
                <label for="promotionCode">Mã khuyến mãi</label>
                <input
                    type="text"
                    id="promotionCode"
                    name="MaCode"
                    maxlength="50"
                    value="{{ old('MaCode') }}"
                    placeholder="Ví dụ: DOLLIE10"
                    required
                >
                @error('MaCode')
                    <small class="promotion-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="promotion-form-group">
                <label for="promotionValue">Phần trăm giảm (%)</label>
                <input
                    type="number"
                    id="promotionValue"
                    name="GiaTriGiam"
                    min="1"
                    max="100"
                    step="0.01"
                    value="{{ old('GiaTriGiam') }}"
                    placeholder="10"
                    required
                >
                @error('GiaTriGiam')
                    <small class="promotion-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="promotion-form-group">
                <label for="promotionMinimum">Đơn tối thiểu (VNĐ)</label>
                <input
                    type="number"
                    id="promotionMinimum"
                    name="GiaTriDonHangToiThieu"
                    min="0"
                    step="1000"
                    value="{{ old('GiaTriDonHangToiThieu') }}"
                    placeholder="200000"
                    required
                >
                @error('GiaTriDonHangToiThieu')
                    <small class="promotion-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="promotion-form-row promotion-form-row-paired">
                <div class="promotion-form-group">
                    <label for="promotionStart">Ngày bắt đầu</label>
                    <input
                        type="text"
                        id="promotionStart"
                        name="NgayBatDau"
                        value="{{ old('NgayBatDau') ? substr(old('NgayBatDau'), 0, 10) : '' }}"
                        placeholder="dd/mm/yy"
                        inputmode="numeric"
                        pattern="[0-9]{2}/[0-9]{2}/[0-9]{2}"
                        title="Nhập ngày theo định dạng dd/mm/yy."
                        required
                    >
                    @error('NgayBatDau')
                        <small class="promotion-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="promotion-form-group">
                    <label for="promotionEnd">Ngày kết thúc</label>
                    <input
                        type="text"
                        id="promotionEnd"
                        name="NgayHetHan"
                        value="{{ old('NgayHetHan') ? substr(old('NgayHetHan'), 0, 10) : '' }}"
                        placeholder="dd/mm/yy"
                        inputmode="numeric"
                        pattern="[0-9]{2}/[0-9]{2}/[0-9]{2}"
                        title="Nhập ngày theo định dạng dd/mm/yy."
                        required
                    >
                    @error('NgayHetHan')
                        <small class="promotion-field-error">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="promotion-form-group">
                <label for="promotionQuantity">Số lượng phát hành</label>
                <input
                    type="number"
                    id="promotionQuantity"
                    name="SoLuongPhatHanh"
                    min="1"
                    step="1"
                    value="{{ old('SoLuongPhatHanh') }}"
                    placeholder="100"
                    required
                >
                @error('SoLuongPhatHanh')
                    <small class="promotion-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="promotion-form-group">
                <label for="promotionStatus">Trạng thái</label>
                <select id="promotionStatus" name="TrangThai" required>
                    <option
                        value="HoatDong"
                        @selected(old('TrangThai', 'HoatDong') === 'HoatDong')
                    >
                        Hoạt động
                    </option>
                    <option
                        value="TamDung"
                        @selected(old('TrangThai') === 'TamDung')
                    >
                        Tạm dừng
                    </option>
                </select>
                @error('TrangThai')
                    <small class="promotion-field-error">{{ $message }}</small>
                @enderror
            </div>

            <div class="category-modal-actions promotion-modal-footer">
                <button
                    type="button"
                    class="category-btn category-btn-cancel"
                    onclick="closePromotionModal()"
                >
                    Hủy
                </button>

                <button
                    type="submit"
                    class="category-btn category-btn-save"
                >
                    Lưu mã khuyến mãi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Popup xác nhận xóa mã khuyến mãi --}}
<div
    class="category-modal-overlay"
    id="promotionDeleteModal"
    aria-hidden="true"
>
    <div
        class="category-modal promotion-delete-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="promotionDeleteTitle"
    >
        <button
            type="button"
            class="category-modal-close"
            onclick="closePromotionDeleteModal()"
            aria-label="Đóng"
        >
            &times;
        </button>

        <h3 id="promotionDeleteTitle">XÁC NHẬN XÓA</h3>

        <p>
            Bạn có chắc muốn xóa mã khuyến mãi
            <strong id="promotionDeleteCode"></strong>
            không?
        </p>

        <p class="promotion-delete-warning">
            Mã đã được cấp cho khách hàng sẽ không thể xóa.
        </p>

        <form id="promotionDeleteForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="category-modal-actions promotion-modal-footer">
                <button
                    type="button"
                    class="category-btn category-btn-cancel"
                    onclick="closePromotionDeleteModal()"
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

{{-- Dữ liệu dùng cho JavaScript --}}
<div
    id="promotionPageConfig"
    data-config="{{ json_encode([
        'storeUrl' => route('admin.promotion.store'),
        'updateUrl' => route('admin.promotion.update', ['id' => '__ID__']),
        'deleteUrl' => route('admin.promotion.destroy', ['id' => '__ID__']),
        'hasValidationErrors' => $errors->any(),
        'oldInput' => old('MaCode'),
        'oldMode' => old('form_mode'),
        'oldId' => old('form_id'),
    ]) }}"
></div>

@endsection