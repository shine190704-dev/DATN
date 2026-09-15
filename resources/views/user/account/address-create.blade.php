@extends('layouts.app')

@section('title', $address ? 'Chỉnh sửa địa chỉ' : 'Thêm địa chỉ')

@section('content')

<main class="account-page address-page">
    <aside class="account-sidebar">
        <div class="account-welcome">
            <img src="{{ asset('images/ICONS/profile_icon.png') }}" alt="Tài khoản">
            <div class="account-welcome-text">
                <span>Xin chào bạn!</span>
                <strong>July</strong>
            </div>
        </div>

        <nav class="account-menu">
            <a href="#" class="account-menu-item">Đơn hàng của tôi</a>
            <a href="#" class="account-menu-item">Theo dõi đơn hàng</a>
            <a href="{{ route('address.index') }}" class="account-menu-item active">Sổ địa chỉ</a>
            <a href="{{ route('profile') }}" class="account-menu-item">Thông tin của tôi</a>
            <a href="#" class="account-menu-item">Yêu cầu hoàn tiền</a>
            <a href="#" class="account-menu-item">Đánh giá của tôi</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="account-menu-item">Đăng xuất</button>
            </form>
        </nav>
    </aside>

    <section class="address-content address-form-page">
        <div class="address-header">
            <div>
                <h1>{{ $address ? 'CHỈNH SỬA ĐỊA CHỈ' : 'THÊM ĐỊA CHỈ' }}</h1>
                <p>Quản lý địa chỉ nhận hàng của bạn</p>
            </div>
            <a href="{{ route('address.index') }}" class="btn-add-address">Sổ địa chỉ</a>
        </div>

        <form
            action="{{ $address ? route('address.update', $address->DiaChiNguoiDungID) : route('address.store') }}"
            method="POST"
            class="address-form-card"
        >
            @csrf
            @if($address)
                @method('PUT')
            @endif

            <label for="TenNguoiNhan">Họ và tên người nhận</label>
            <input id="TenNguoiNhan" name="TenNguoiNhan" value="{{ old('TenNguoiNhan', $address->TenNguoiNhan ?? '') }}" required>

            <label for="SoDienThoai">Số điện thoại</label>
            <input id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai', $address->SoDienThoai ?? '') }}" required>

            <label for="DiaChi">Địa chỉ cụ thể</label>
            <input id="DiaChi" name="DiaChi" value="{{ old('DiaChi', $address->DiaChi ?? '') }}" required>

            <label for="ThanhPho">Thành phố</label>

<select
    id="ThanhPho"
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
            {{ old('ThanhPho', $address->ThanhPho ?? '') == $province ? 'selected' : '' }}
        >
            {{ $province }}
        </option>

    @endforeach

</select>
                <a href="{{ route('address.index') }}" class="btn-cancel-address">Hủy</a>
                <button type="submit" class="btn-save-address">{{ $address ? 'Lưu thay đổi' : 'Lưu địa chỉ' }}</button>
            </div>
        </form>
    </section>
</main>

@endsection
