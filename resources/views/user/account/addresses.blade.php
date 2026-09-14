@extends('layouts.app')

@section('title', 'Sổ địa chỉ')

@push('styles')
    @vite('resources/css/user/address.css')
@endpush

@section('content')

<div class="account-page">

    {{-- SIDEBAR --}}
    <aside class="account-sidebar">

        <div class="account-sidebar-title">
            Tài khoản
        </div>

        <a href="{{ route('profile') }}" class="account-menu-item">
            <span>Thông tin cá nhân</span>
        </a>

        <a href="{{ route('address.index') }}" class="account-menu-item active">
            <span>Sổ địa chỉ</span>
        </a>

        <a href="#" class="account-menu-item">
            <span>Lịch sử mua hàng</span>
        </a>

        <a href="#" class="account-menu-item">
            <span>Theo dõi đơn hàng</span>
        </a>

        <a href="#" class="account-menu-item">
            <span>Đánh giá</span>
        </a>

        <a href="#" class="account-menu-item">
            <span>Đăng xuất</span>
        </a>

    </aside>


    {{-- CONTENT --}}
    <main class="address-content">

        <div class="address-header">

            <div>
                <h1>Sổ địa chỉ</h1>

                <p>
                    Quản lý địa chỉ nhận hàng của bạn
                </p>
            </div>

            <button
                type="button"
                class="btn-add-address"
                onclick="document.getElementById('add-address-form').scrollIntoView({ behavior: 'smooth' })"
            >
                + Thêm địa chỉ
            </button>

        </div>


        {{-- THÔNG BÁO --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif


        {{-- LỖI VALIDATION --}}
        @if($errors->any())
            <div class="alert alert-error">

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- DANH SÁCH ĐỊA CHỈ --}}
        <section class="address-section">

            <div class="section-title">
                Địa chỉ của tôi
            </div>

            @if($addresses->count() > 0)

                @foreach($addresses as $address)

                    <div class="address-card">

                        <div class="address-card-top">

                            <div class="receiver-info">

                                <div class="receiver-name">
                                    {{ $address->TenNguoiNhan }}

                                    @if($address->MacDinh == 1)
                                        <span class="default-badge">
                                            Mặc định
                                        </span>
                                    @endif

                                </div>

                                <div class="receiver-phone">
                                    {{ $address->SoDienThoai }}
                                </div>

                            </div>


                            <div class="address-actions">

                                {{-- ĐẶT LÀM MẶC ĐỊNH --}}
                                @if($address->MacDinh != 1)

                                    <form
                                        action="{{ route('address.default', $address->DiaChiNguoiDungID) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn-default"
                                        >
                                            Đặt làm mặc định
                                        </button>

                                    </form>

                                @endif


                                {{-- XÓA --}}
                                @if($addresses->count() > 1)

                                    <form
                                        action="{{ route('address.destroy', $address->DiaChiNguoiDungID) }}"
                                        method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này không?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-delete"
                                        >
                                            Xóa
                                        </button>

                                    </form>

                                @endif

                            </div>

                        </div>


                        {{-- ĐỊA CHỈ --}}
                        <div class="address-detail">

                            <span class="address-label">
                                Địa chỉ:
                            </span>

                            {{ $address->DiaChi }},
                            {{ $address->ThanhPho }}

                        </div>


                        {{-- FORM CHỈNH SỬA --}}
                        <details class="edit-address">

                            <summary>
                                Chỉnh sửa địa chỉ
                            </summary>

                            <form
                                action="{{ route('address.update', $address->DiaChiNguoiDungID) }}"
                                method="POST"
                                class="address-form"
                            >

                                @csrf
                                @method('PUT')


                                <div class="form-group">

                                    <label>
                                        Tên người nhận
                                    </label>

                                    <input
                                        type="text"
                                        name="TenNguoiNhan"
                                        value="{{ $address->TenNguoiNhan }}"
                                        required
                                    >

                                </div>


                                <div class="form-group">

                                    <label>
                                        Số điện thoại
                                    </label>

                                    <input
                                        type="text"
                                        name="SoDienThoai"
                                        value="{{ $address->SoDienThoai }}"
                                        maxlength="10"
                                        required
                                    >

                                </div>


                                <div class="form-group">

                                    <label>
                                        Thành phố / Tỉnh
                                    </label>

                                    <select
                                        name="ThanhPho"
                                        required
                                    >

                                        <option value="">
                                            -- Chọn tỉnh / thành phố --
                                        </option>

                                        @php
                                            $provinces = [
                                                'Tỉnh Lai Châu',
                                                'Tỉnh Điện Biên',
                                                'Tỉnh Sơn La',
                                                'Tỉnh Lạng Sơn',
                                                'Tỉnh Cao Bằng',
                                                'Tỉnh Tuyên Quang',
                                                'Tỉnh Lào Cai',
                                                'Tỉnh Thái Nguyên',
                                                'Tỉnh Phú Thọ',
                                                'Tỉnh Bắc Ninh',
                                                'Tỉnh Hưng Yên',
                                                'Tỉnh Ninh Bình',
                                                'Tỉnh Thanh Hóa',
                                                'Tỉnh Nghệ An',
                                                'Tỉnh Hà Tĩnh',
                                                'Tỉnh Quảng Trị',
                                                'Tỉnh Quảng Ngãi',
                                                'Tỉnh Gia Lai',
                                                'Tỉnh Khánh Hòa',
                                                'Tỉnh Đắk Lắk',
                                                'Tỉnh Lâm Đồng',
                                                'Tỉnh Đồng Nai',
                                                'Tỉnh Tây Ninh',
                                                'Tỉnh Đồng Tháp',
                                                'Tỉnh Vĩnh Long',
                                                'Tỉnh An Giang',
                                                'Tỉnh Cà Mau',
                                                'Tỉnh Quảng Ninh',
                                                'Thành phố Hà Nội',
                                                'Thành phố Hải Phòng',
                                                'Thành phố Huế',
                                                'Thành phố Đà Nẵng',
                                                'Thành phố Hồ Chí Minh',
                                                'Thành phố Cần Thơ'
                                            ];
                                        @endphp

                                        @foreach($provinces as $province)

                                            <option
                                                value="{{ $province }}"
                                                {{ $address->ThanhPho == $province ? 'selected' : '' }}
                                            >
                                                {{ $province }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <div class="form-group">

                                    <label>
                                        Địa chỉ cụ thể
                                    </label>

                                    <textarea
                                        name="DiaChi"
                                        rows="3"
                                        required
                                    >{{ $address->DiaChi }}</textarea>

                                </div>


                                {{-- KHÔNG CÓ CHECKBOX MẶC ĐỊNH Ở ĐÂY --}}

                                <button
                                    type="submit"
                                    class="btn-save-address"
                                >
                                    Lưu thay đổi
                                </button>

                            </form>

                        </details>

                    </div>

                @endforeach

            @else

                <div class="empty-address">

                    <div class="empty-address-icon">
                        📍
                    </div>

                    <h3>
                        Chưa có địa chỉ nào
                    </h3>

                    <p>
                        Bạn chưa thêm địa chỉ nhận hàng.
                    </p>

                    <p>
                        Hãy thêm địa chỉ để thuận tiện cho những lần mua hàng sau.
                    </p>

                </div>

            @endif

        </section>


        {{-- THÊM ĐỊA CHỈ --}}
        @if($addresses->count() < 5)

            <section
                class="add-address-section"
                id="add-address-form"
            >

                <div class="section-title">
                    Thêm địa chỉ mới
                </div>

                <form
                    action="{{ route('address.store') }}"
                    method="POST"
                    class="address-form"
                >

                    @csrf


                    <div class="form-group">

                        <label>
                            Tên người nhận
                        </label>

                        <input
                            type="text"
                            name="TenNguoiNhan"
                            placeholder="Nhập tên người nhận"
                            value="{{ old('TenNguoiNhan') }}"
                            maxlength="200"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Số điện thoại
                        </label>

                        <input
                            type="text"
                            name="SoDienThoai"
                            placeholder="Nhập số điện thoại"
                            value="{{ old('SoDienThoai') }}"
                            maxlength="10"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Tỉnh / Thành phố
                        </label>

                        <select
                            name="ThanhPho"
                            required
                        >

                            <option value="">
                                -- Chọn tỉnh / thành phố --
                            </option>

                            @foreach($provinces as $province)

                                <option
                                    value="{{ $province }}"
                                    {{ old('ThanhPho') == $province ? 'selected' : '' }}
                                >
                                    {{ $province }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="form-group">

                        <label>
                            Địa chỉ cụ thể
                        </label>

                        <textarea
                            name="DiaChi"
                            rows="3"
                            placeholder="Số nhà, tên đường..."
                            maxlength="225"
                            required
                        >{{ old('DiaChi') }}</textarea>

                    </div>


                    {{-- KHÔNG CHO CHỌN MẶC ĐỊNH KHI TẠO --}}

                    <div class="form-note">
                        <span>ⓘ</span>
                        Địa chỉ đầu tiên sẽ tự động được đặt làm địa chỉ mặc định.
                    </div>


                    <button
                        type="submit"
                        class="btn-save-address"
                    >
                        Thêm địa chỉ
                    </button>

                </form>

            </section>

        @else

            <div class="address-limit">
                Bạn đã đạt giới hạn tối đa 5 địa chỉ.
            </div>

        @endif

    </main>

</div>

@endsection