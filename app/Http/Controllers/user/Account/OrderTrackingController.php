<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderTrackingController extends Controller
{
    public function index()
    {
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = session('NguoiDungID');

        // =========================
        // THÔNG TIN NGƯỜI DÙNG
        // =========================
        $user = DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        // =========================
        // DANH MỤC CHO NAVBAR
        // =========================
        $danhMucs = DB::table('DanhMuc')
            ->get();

        // =========================
        // LẤY ĐƠN HÀNG
        // =========================
        $orders = DB::table('DonHang')
            ->where('NguoiDungID', $nguoiDungID)
            ->whereNotIn('TrangThai', [
                'DaHuy',
                'Đã hủy'
            ])
            ->orderByDesc('DonHangID')
            ->get();

        // =========================
        // LẤY SẢN PHẨM TRONG ĐƠN
        // =========================
        $items = DB::table('ChiTietDonHang')
            ->whereIn('DonHangID', $orders->pluck('DonHangID'))
            ->select(
                'DonHangID',
                'TenSanPham',
                'MauSac',
                'KichThuoc',
                'HinhAnh',
                'SoLuong',
                'GiaTaiThoiDiemMua'
            )
            ->get()
            ->groupBy('DonHangID');

        // Gắn sản phẩm vào từng đơn hàng
        $orders->transform(function ($order) use ($items) {

            $order->items = $items->get(
                $order->DonHangID,
                collect()
            );

            return $order;
        });

        return view(
            'user.account.order-tracking',
            compact(
                'user',
                'danhMucs',
                'orders'
            )
        );
    }
}