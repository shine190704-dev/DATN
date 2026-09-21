<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * ========================================
     * TRANG ĐÁNH GIÁ CỦA TÔI
     * ========================================
     */
    public function index()
    {
        // Kiểm tra đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $userId = (int) session('NguoiDungID');


        // ========================================
        // SẢN PHẨM CHƯA ĐÁNH GIÁ
        // ========================================

        $pendingReviews = DB::table('ChiTietDonHang as ct')

            ->join(
                'DonHang as dh',
                'dh.DonHangID',
                '=',
                'ct.DonHangID'
            )

            ->where(
                'dh.NguoiDungID',
                $userId
            )

            // Chỉ đơn hàng đã hoàn thành
            ->where(
                'dh.TrangThai',
                'HoanThanh'
            )

            // Chưa có đánh giá
            ->whereNotExists(function ($query) {

                $query->select(DB::raw(1))

                    ->from('DanhGia as dg')

                    ->whereColumn(
                        'dg.DonHangID',
                        'ct.DonHangID'
                    )

                    ->whereColumn(
                        'dg.SanPhamID',
                        'ct.SanPhamID'
                    )

                    ->whereColumn(
                        'dg.NguoiDungID',
                        'dh.NguoiDungID'
                    );

            })

            ->select(
                'ct.ChiTietDonHangID',
                'ct.DonHangID',
                'ct.SanPhamID',
                'ct.TenSanPham',
                'ct.MauSac',
                'ct.HinhAnh',
                'ct.KichThuoc',

                'dh.MaDonHang',
                'dh.NgayCapNhat as NgayNhan'
            )

            ->orderByDesc(
                'ct.DonHangID'
            )

            ->get();


        // ========================================
        // SẢN PHẨM ĐÃ ĐÁNH GIÁ
        // ========================================

        $reviewedReviews = DB::table('DanhGia as dg')

            ->join(
                'DonHang as dh',
                'dh.DonHangID',
                '=',
                'dg.DonHangID'
            )

            ->join(
                'SanPham as sp',
                'sp.SanPhamID',
                '=',
                'dg.SanPhamID'
            )

            ->leftJoin(
                'ChiTietDonHang as ct',
                function ($join) {

                    $join->on(
                        'ct.DonHangID',
                        '=',
                        'dg.DonHangID'
                    );

                    $join->on(
                        'ct.SanPhamID',
                        '=',
                        'dg.SanPhamID'
                    );

                }
            )

            ->where(
                'dg.NguoiDungID',
                $userId
            )

            ->select(
                'dg.DanhGiaID',

                'dg.DiemDanhGia as SoSao',

                'dg.BinhLuan as NoiDung',

                'dg.TrangThai',

                'dg.NgayTao',

                'dg.SanPhamID',

                'dg.DonHangID',

                'dh.MaDonHang',

                'dh.NgayCapNhat as NgayNhan',

                'sp.TenSanPham',

                'ct.MauSac',

                'ct.HinhAnh',

                'ct.KichThuoc'
            )

            ->orderByDesc(
                'dg.DanhGiaID'
            )

            ->get();


        // ========================================
        // ĐẾM
        // ========================================

        $pendingCount =
            $pendingReviews->count();

        $reviewedCount =
            $reviewedReviews->count();


        // ========================================
        // USER
        // ========================================

        $user = DB::table('NguoiDung')
            ->where(
                'NguoiDungID',
                $userId
            )
            ->first();


        // ========================================
        // DANH MỤC
        // ========================================

        $danhMucs = DB::table('DanhMuc')
            ->get();


        // ========================================
        // VIEW
        // ========================================

        return view(
            'user.account.reviews',
            [
                'pendingReviews' =>
                    $pendingReviews,

                'reviewedReviews' =>
                    $reviewedReviews,

                'pendingCount' =>
                    $pendingCount,

                'reviewedCount' =>
                    $reviewedCount,

                'user' =>
                    $user,

                'danhMucs' =>
                    $danhMucs,
            ]
        );
    }


    /**
     * ========================================
     * LƯU ĐÁNH GIÁ
     * ========================================
     */
    public function store(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $userId = (int) session('NguoiDungID');


        // ========================================
        // VALIDATE
        // ========================================

        $data = $request->validate(
            [
                'SanPhamID' => [
                    'required',
                    'integer',
                ],

                'DonHangID' => [
                    'required',
                    'integer',
                ],

                'DiemDanhGia' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:5',
                ],

                'BinhLuan' => [
                    'nullable',
                    'string',
                    'max:500',
                ],
            ],
            [
                'SanPhamID.required' =>
                    'Sản phẩm không hợp lệ.',

                'SanPhamID.integer' =>
                    'Sản phẩm không hợp lệ.',

                'DonHangID.required' =>
                    'Đơn hàng không hợp lệ.',

                'DonHangID.integer' =>
                    'Đơn hàng không hợp lệ.',

                'DiemDanhGia.required' =>
                    'Vui lòng chọn số sao.',

                'DiemDanhGia.integer' =>
                    'Số sao không hợp lệ.',

                'DiemDanhGia.min' =>
                    'Vui lòng chọn từ 1 đến 5 sao.',

                'DiemDanhGia.max' =>
                    'Vui lòng chọn từ 1 đến 5 sao.',

                'BinhLuan.max' =>
                    'Bình luận tối đa 500 ký tự.',
            ]
        );


        // ========================================
        // KIỂM TRA SẢN PHẨM CÓ THUỘC ĐƠN HÀNG
        // CỦA NGƯỜI DÙNG HAY KHÔNG
        // ========================================

        $item = DB::table('ChiTietDonHang as ct')

            ->join(
                'DonHang as dh',
                'dh.DonHangID',
                '=',
                'ct.DonHangID'
            )

            ->where(
                'ct.DonHangID',
                $data['DonHangID']
            )

            ->where(
                'ct.SanPhamID',
                $data['SanPhamID']
            )

            ->where(
                'dh.NguoiDungID',
                $userId
            )

            ->where(
                'dh.TrangThai',
                'HoanThanh'
            )

            ->select(
                'ct.SanPhamID',
                'ct.DonHangID'
            )

            ->first();


        if (!$item) {

            return back()
                ->withInput()
                ->withErrors([
                    'DiemDanhGia' =>
                        'Sản phẩm không thuộc đơn hàng của bạn hoặc đơn hàng chưa hoàn thành.',
                ]);

        }


        // ========================================
        // KIỂM TRA ĐÃ ĐÁNH GIÁ CHƯA
        // ========================================

        $alreadyReviewed = DB::table('DanhGia')

            ->where(
                'NguoiDungID',
                $userId
            )

            ->where(
                'SanPhamID',
                $item->SanPhamID
            )

            ->where(
                'DonHangID',
                $item->DonHangID
            )

            ->exists();


        if ($alreadyReviewed) {

            return back()
                ->withInput()
                ->withErrors([
                    'DiemDanhGia' =>
                        'Bạn đã đánh giá sản phẩm này rồi.',
                ]);

        }


        // ========================================
        // LƯU ĐÁNH GIÁ
        // ========================================

        DB::table('DanhGia')->insert([

            'DiemDanhGia' =>
                (int) $data['DiemDanhGia'],

            'BinhLuan' =>
                $data['BinhLuan'] ?? null,

            'TrangThai' =>
                'HienThi',

            'NgayTao' =>
                now(),

            'NgayCapNhat' =>
                now(),

            'NguoiDungID' =>
                $userId,

            'SanPhamID' =>
                $item->SanPhamID,

            'DonHangID' =>
                $item->DonHangID,

        ]);


        // ========================================
        // THÔNG BÁO
        // ========================================

        return redirect()
            ->route('reviews.index')
            ->with(
                'success',
                'Đánh giá sản phẩm đã được gửi thành công.'
            );
    }
}