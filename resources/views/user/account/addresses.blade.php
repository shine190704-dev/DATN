@extends('layouts.app')

@section('title', 'Sổ địa chỉ')

@section('content')

<main class="account-page">

    {{-- =========================
         SIDEBAR
    ========================== --}}
    <aside class="account-sidebar">

        <div class="account-welcome">

            <img
                src="{{ asset('images/ICONS/profile_icon.png') }}"
                alt="Tài khoản"
            >

            <div class="account-welcome-text">

                <span>Xin chào bạn!</span>

                <strong>
                    July
                </strong>

            </div>

        </div>


        <nav class="account-menu">

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('order.*') ? 'active' : '' }}"
            >
                Đơn hàng của tôi
            </a>

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('tracking.*') ? 'active' : '' }}"
            >
                Theo dõi đơn hàng
            </a>

            <a
                href="{{ route('address.index') }}"
                class="account-menu-item {{ request()->routeIs('address.*') ? 'active' : '' }}"
            >
                Sổ địa chỉ
            </a>

            <a
                href="{{ route('profile') }}"
                class="account-menu-item {{ request()->routeIs('profile') ? 'active' : '' }}"
            >
                Thông tin của tôi
            </a>

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('refund.*') ? 'active' : '' }}"
            >
                Yêu cầu hoàn tiền
            </a>

            <a
                href="#"
                class="account-menu-item {{ request()->routeIs('review.*') ? 'active' : '' }}"
            >
                Đánh giá của tôi
            </a>

            <form action="{{ route('logout') }}" method="POST">

                @csrf

                <button
                    type="submit"
                    class="account-menu-item"
                >
                    Đăng xuất
                </button>

            </form>

        </nav>

    </aside>


    {{-- =========================
         NỘI DUNG
    ========================== --}}
    <section class="address-content">

        {{-- HEADER --}}
        <div class="address-header">

            <div>

                <h1>
                    SỔ ĐỊA CHỈ
                </h1>

                <p>
                    Quản lý địa chỉ nhận hàng của bạn
                </p>

            </div>


            {{-- THÊM ĐỊA CHỈ --}}
            @if($addresses->count() < 5)

                <a
                    href="{{ route('address.create') }}"
                    class="btn-add-address"
                >
                    + Thêm địa chỉ
                </a>

            @endif

        </div>


        {{-- =========================
             THÔNG BÁO
        ========================== --}}

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


        @if($errors->any())

            <div class="alert alert-error">

                <ul>

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- =========================
             DANH SÁCH ĐỊA CHỈ
        ========================== --}}

        <section class="address-section">

            @if($addresses->count() > 0)

                @foreach($addresses as $address)

                    <div class="address-card">

                        <div class="address-card-top">

                            <div class="receiver-info">

                                <div class="receiver-heading">
                                    <span class="receiver-name">{{ $address->TenNguoiNhan }}</span>
                                    <span class="receiver-phone">{{ $address->SoDienThoai }}</span>
                                </div>

                                <div class="address-detail">
                                    {{ $address->DiaChi }}, {{ $address->ThanhPho }}
                                </div>

                            </div>

                            @if($address->MacDinh == 1)
                                <span class="default-badge">Mặc định</span>
                            @endif

                        </div>

                        <div class="address-actions">

                            @if($address->MacDinh != 1)
                                <form
                                    action="{{ route('address.default', $address->DiaChiNguoiDungID) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có muốn đổi địa chỉ mặc định mới thành địa chỉ này không?')"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-default">
                                        Đặt làm mặc định
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('address.edit', $address->DiaChiNguoiDungID) }}" class="btn-edit">
                                Sửa
                            </a>

                            @if($addresses->count() > 1)
                                <form
                                    action="{{ route('address.destroy', $address->DiaChiNguoiDungID) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa địa chỉ này không?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Xóa</button>
                                </form>
                            @endif

                        </div>

                    </div>

                @endforeach


            @else

                {{-- CHƯA CÓ ĐỊA CHỈ --}}
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


        {{-- ĐỦ 5 ĐỊA CHỈ --}}
        @if($addresses->count() >= 5)

            <div class="address-limit">

                Bạn đã đạt giới hạn tối đa 5 địa chỉ.

            </div>

        @endif

    </section>

</main>

@endsection