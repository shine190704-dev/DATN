@extends('layouts.app')

@section('title', 'Sổ địa chỉ')

@section('content')

<main class="account-page">

    {{-- SIDEBAR --}}
    @include('partials.account-sidebar')


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

                                    <span class="receiver-name">
                                        {{ $address->TenNguoiNhan }}
                                    </span>

                                    <span class="receiver-phone">
                                        {{ $address->SoDienThoai }}
                                    </span>

                                </div>

                                <div class="address-detail">
                                    {{ $address->DiaChi }}, {{ $address->ThanhPho }}
                                </div>

                            </div>


                            {{-- ĐỊA CHỈ MẶC ĐỊNH --}}
                            @if($address->MacDinh == 1)

                                <span class="default-badge">
                                    Mặc định
                                </span>

                            @endif

                        </div>


                        {{-- =========================
                             CÁC NÚT
                        ========================== --}}

                        <div class="address-actions">

                            {{-- ĐẶT LÀM MẶC ĐỊNH --}}
                            @if($address->MacDinh != 1)

                                <form
                                    action="{{ route('address.default', $address->DiaChiNguoiDungID) }}"
                                    method="POST"
                                    class="set-default-form"
                                    data-default-form
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="button"
                                        class="btn-default"
                                        data-default-button
                                    >
                                        Đặt làm mặc định
                                    </button>

                                </form>

                            @endif


                            {{-- SỬA --}}
                            <a
                                href="{{ route('address.edit', $address->DiaChiNguoiDungID) }}"
                                class="btn-edit"
                            >
                                Sửa
                            </a>


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


{{-- =====================================================
     POPUP XÁC NHẬN ĐỔI ĐỊA CHỈ MẶC ĐỊNH
===================================================== --}}

<dialog
    class="address-confirm-dialog"
    id="addressConfirmDialog"
>

    <div class="address-confirm-popup">

        <p>
            Bạn có muốn đổi địa chỉ mặc định mới thành địa chỉ này không?
        </p>

        <div class="address-confirm-actions">

            {{-- KHÔNG --}}
            <button
                type="button"
                class="address-confirm-no"
                id="addressConfirmNo"
            >
                Không
            </button>


            {{-- CÓ --}}
            <button
                type="button"
                class="address-confirm-yes"
                id="addressConfirmYes"
            >
                Có
            </button>

        </div>

    </div>

</dialog>


{{-- =====================================================
     FORM ẨN ĐỂ GỬI REQUEST
===================================================== --}}

<form
    method="POST"
    id="addressConfirmForm"
    style="display: none;"
>
    @csrf
    @method('PATCH')
</form>


@endsection