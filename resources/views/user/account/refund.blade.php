@extends('layouts.app')

@section('title', 'Yêu cầu hoàn tiền')

@section('content')

@php
    // ========================================
    // TRẠNG THÁI YÊU CẦU HOÀN TIỀN
    // ========================================

    $statusLabels = [
        'ChoXuLy'    => 'Chờ xử lý',
        'DaDuyet'    => 'Đã duyệt',
        'TuChoi'     => 'Đã từ chối',

        // Trường hợp DB đang lưu trực tiếp bằng tiếng Việt
        'Chờ xử lý'  => 'Chờ xử lý',
        'Đã duyệt'   => 'Đã duyệt',
        'Đã từ chối' => 'Đã từ chối',
    ];


    // ========================================
    // CHUYỂN TRẠNG THÁI VỀ MÃ CHUẨN
    // ========================================

    $codeMap = [
        'Chờ xử lý'  => 'ChoXuLy',
        'Đã duyệt'   => 'DaDuyet',
        'Đã từ chối' => 'TuChoi',
    ];


    // ========================================
    // LỜI NHẮN MẶC ĐỊNH
    // ========================================

    $defaultNotes = [
        'ChoXuLy' => 'Yêu cầu của bạn đang chờ shop xử lý.',
        'DaDuyet' => 'Yêu cầu đã được duyệt, tiền sẽ hoàn trong 3-5 ngày làm việc.',
        'TuChoi'  => 'Yêu cầu chưa được chấp nhận, vui lòng liên hệ CSKH để được hỗ trợ thêm.',
    ];


    // ========================================
    // LỖI ẢNH MINH CHỨNG
    // ========================================

    $imageError = $errors->first('AnhMinhChung')
        ?: collect($errors->keys())
            ->filter(function ($key) {
                return str_starts_with($key, 'AnhMinhChung.');
            })
            ->map(function ($key) {
                return $errors->first($key);
            })
            ->first();
@endphp


<main class="account-page">

    {{-- ========================================
         SIDEBAR
    ========================================= --}}

    @include('partials.account-sidebar')


    {{-- ========================================
         NỘI DUNG
    ========================================= --}}

    <section class="account-content refund-content">


        {{-- ========================================
             TIÊU ĐỀ
        ========================================= --}}

        <div class="refund-head">

            <h1>
                Yêu cầu hoàn tiền
            </h1>

            <button
                type="button"
                class="refund-create-btn"
                data-refund-open
            >
                Tạo yêu cầu
            </button>

        </div>


        {{-- ========================================
             THÔNG BÁO THÀNH CÔNG
        ========================================= --}}

        @if(session('success'))

            <div class="refund-alert refund-alert-success">
                {{ session('success') }}
            </div>

        @endif


        @if(session('error'))
            <div class="refund-alert refund-alert-warning">
                {{ session('error') }}
            </div>
        @endif


        {{-- ========================================
             THÔNG BÁO ĐIỀU KIỆN ĐƠN HÀNG
        ========================================= --}}

        @if($notice)

            <div class="refund-alert refund-alert-warning">
                {{ $notice }}
            </div>

        @endif


        {{-- ========================================
             DANH SÁCH YÊU CẦU HOÀN TIỀN
        ========================================= --}}

        @forelse($requests as $r)

            @php

                // Mã trạng thái
                $code = $codeMap[$r->TrangThai]
                    ?? $r->TrangThai;


                // ========================================
                // LẤY ẢNH MINH CHỨNG
                // ========================================

                $refundImages = collect(
                    json_decode(
                        $r->AnhMinhChung ?? '[]',
                        true
                    ) ?: []
                );

            @endphp


            <div class="refund-card">


                {{-- ========================================
                     PHẦN TRÊN CỦA CARD
                ========================================= --}}

                <div class="refund-card-main">


                    {{-- ========================================
                         MÃ YÊU CẦU + MÃ ĐƠN HÀNG + TRẠNG THÁI
                    ========================================= --}}

                    <div class="refund-card-top">

                        <p class="refund-card-code">

                            Yêu cầu
                            <strong>
                                #{{ $r->MaYeuCau }}
                            </strong>

                            <span>
                                Đơn hàng
                                <strong>
                                    #{{ $r->MaDonHang }}
                                </strong>
                            </span>

                        </p>


                        <span class="refund-status">

                            {{ $statusLabels[$r->TrangThai] ?? $r->TrangThai }}

                        </span>

                    </div>


                    {{-- ========================================
                         MÔ TẢ
                    ========================================= --}}

                    <p class="refund-card-desc">

                        {{ $r->MoTa }}

                    </p>


                    {{-- ========================================
                         ẢNH MINH CHỨNG
                    ========================================= --}}

                    @if($refundImages->isNotEmpty())

                        <div class="refund-card-images">

                            @foreach($refundImages as $path)

                                <a
                                    href="{{ asset('storage/' . $path) }}"
                                    target="_blank"
                                    rel="noopener"
                                >

                                    <img
                                        src="{{ asset('storage/' . $path) }}"
                                        alt="Ảnh minh chứng {{ $loop->iteration }}"
                                    >

                                </a>

                            @endforeach

                        </div>

                    @endif


                </div>


                {{-- ========================================
                     PHẦN GHI CHÚ XỬ LÝ
                ========================================= --}}

                <div class="refund-card-note">

                    <div class="refund-card-note-text">
                        <p>{{ $r->GhiChuXuLy ?: ($defaultNotes[$code] ?? '') }}</p>

                        <small>
                            Gửi ngày
                            {{ \Carbon\Carbon::parse($r->NgayYeuCau)->format('d/m/Y') }}
                        </small>
                    </div>

                    @if($code === 'ChoXuLy')
                        <button
                            type="button"
                            class="refund-cancel-btn"
                            data-refund-cancel-url="{{ route('refund.cancel', $r->YeuCauHoanTienID) }}"
                        >
                        Hủy yêu cầu
                        </button>
                    @endif

                </div>


            </div>


        @empty


            {{-- ========================================
                 CHƯA CÓ YÊU CẦU
            ========================================= --}}

            <div class="refund-empty">

                <p>
                    Bạn chưa có yêu cầu hoàn tiền nào.
                </p>

            </div>


        @endforelse


    </section>

</main>



{{-- =========================================================
     POPUP TẠO YÊU CẦU HOÀN TIỀN
========================================================= --}}

<dialog
    class="refund-dialog"
    id="refundDialog"
    data-auto-open="{{ ($errors->any() || filled($preselect)) ? '1' : '0' }}"
>


    <form
        method="POST"
        action="{{ route('refund.store') }}"
        enctype="multipart/form-data"
        novalidate
    >

        @csrf


        {{-- ========================================
             TIÊU ĐỀ POPUP
        ========================================= --}}

        <h2>
            Tạo yêu cầu hoàn tiền
        </h2>


        @if($eligibleOrders->isEmpty())


            {{-- ========================================
                 KHÔNG CÓ ĐƠN ĐỦ ĐIỀU KIỆN
            ========================================= --}}

            <p class="refund-none">

                Bạn chưa có đơn hàng nào đủ điều kiện hoàn tiền.

                Chỉ đơn đã giao trong vòng
                {{ $refundDays }} ngày
                và chưa có yêu cầu mới được hoàn tiền.

            </p>


            <div class="refund-actions">

                <button
                    type="button"
                    class="refund-btn-solid"
                    data-refund-close
                >
                    Đóng
                </button>

            </div>


        @else


            {{-- ========================================
                 CHỌN ĐƠN HÀNG
            ========================================= --}}

            <div class="refund-field">

                <label for="refundOrder">
                    Chọn đơn hàng
                </label>


                <select
                    id="refundOrder"
                    name="DonHangID"
                    required
                >

                    <option value="">
                        -- Chọn đơn hàng --
                    </option>


                    @foreach($eligibleOrders as $o)

                        <option
                            value="{{ $o->DonHangID }}"

                            @selected(
                                (string) old(
                                    'DonHangID',
                                    $preselect
                                )
                                ===
                                (string) $o->DonHangID
                            )
                        >

                            {{ $o->Nhan }}

                        </option>

                    @endforeach

                </select>


                @error('DonHangID')

                    <span class="refund-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>



            {{-- ========================================
                 MÔ TẢ
            ========================================= --}}

            <div class="refund-field">

                <label for="refundDesc">
                    Mô tả
                </label>


                <textarea
                    id="refundDesc"
                    name="MoTa"
                    required
                >{{ old('MoTa') }}</textarea>


                @error('MoTa')

                    <span class="refund-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>



            {{-- ========================================
                 ẢNH MINH CHỨNG
            ========================================= --}}

            <div class="refund-field">

                <label>
                    Ảnh minh chứng
                </label>


                {{-- KHU VỰC XEM TRƯỚC ẢNH --}}

                <div
                    class="refund-images"
                    id="refundPreview"
                >

                    <button
                        type="button"
                        class="refund-add"
                        id="refundAddImage"
                        aria-label="Thêm ảnh"
                    >
                        +
                    </button>

                </div>


                {{-- INPUT FILE --}}

                <input
                    type="file"
                    id="refundImages"
                    name="AnhMinhChung[]"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    hidden
                >


                {{-- LỖI ẢNH --}}

                @if($imageError)

                    <span class="refund-error">
                        {{ $imageError }}
                    </span>

                @endif


                @error('AnhMinhChung')

                    <span class="refund-error">
                        {{ $message }}
                    </span>

                @enderror

            </div>



            {{-- ========================================
                 NÚT
            ========================================= --}}

            <div class="refund-actions">


                <button
                    type="button"
                    class="refund-btn-solid"
                    data-refund-close
                >
                    Hủy
                </button>


                <button
                    type="submit"
                    class="refund-btn-outline"
                >
                    Gửi yêu cầu
                </button>


            </div>


        @endif


    </form>

</dialog>

{{-- HỘP XÁC NHẬN HỦY YÊU CẦU --}}
<dialog class="refund-dialog refund-confirm" id="refundCancelDialog">

    <form method="POST" id="refundCancelForm">
        @csrf
        @method('DELETE')

        <p>Bạn có chắc muốn hủy yêu cầu hoàn tiền này không?</p>

        <div class="refund-actions">
            <button type="button" class="refund-btn-solid" id="refundCancelClose">
                Không
            </button>

            <button type="submit" class="refund-btn-outline">
                Hủy yêu cầu
            </button>
        </div>
    </form>

</dialog>


@endsection