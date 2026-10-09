<?php

namespace App\Http\Controllers\user\Account;


use App\Models\DanhMuc;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\NguoiDung;
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
        $user = NguoiDung::query()->from('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        // =========================
        // DANH MỤC CHO NAVBAR
        // =========================
        $danhMucs = DanhMuc::query()
            ->get();

        // =========================
        // LẤY ĐƠN HÀNG
        // =========================
        $orders = DonHang::query()->from('DonHang')
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
        $items = ChiTietDonHang::query()->from('ChiTietDonHang')
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