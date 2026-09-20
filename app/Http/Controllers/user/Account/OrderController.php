<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // =========================================================
    // CÁC TAB TRÊN GIAO DIỆN
    // =========================================================

        private const TABS = [

        // Đơn mới
            'ChoXacNhan' => [
                'MoiTao',
                'ChoXacNhan',
                'Chờ xác nhận',
            ],

         // Đã xác nhận
            'DaXacNhan' => [
        'DaXacNhan',
        'Đã xác nhận',
         ],

            // Đang giao
            'DangGiao' => [
                'DangGiao',
               'DangGiaoHang',
               'Đang giao hàng',
            ],

            // Đã nhận
            'DaGiao' => [
            'HoanThanh',
            ],

            // Đã hủy
            'DaHuy' => [
             'DaHuy',
             'Đã hủy',
            ],
        ];

    // =========================================================
    // DANH SÁCH ĐƠN HÀNG CỦA TÔI
    // =========================================================

    public function index()
    {
        // Chưa đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = (int) session('NguoiDungID');

        $status = request('status');


        // =====================================================
        // THÔNG TIN NGƯỜI DÙNG
        // =====================================================

        $user = DB::table('NguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->first();


        // =====================================================
        // DANH MỤC CHO NAVBAR
        // =====================================================

        $danhMucs = DB::table('DanhMuc')
            ->get();


        // =====================================================
        // LẤY ĐƠN HÀNG
        // =====================================================

        $orders = DB::table('DonHang')
            ->where('NguoiDungID', $nguoiDungID)

            ->when(
                isset(self::TABS[$status]),
                function ($query) use ($status) {
                    $query->whereIn(
                        'TrangThai',
                        self::TABS[$status]
                    );
                }
            )

            ->orderByDesc('DonHangID')
            ->get();


        // =====================================================
        // LẤY SẢN PHẨM TRONG ĐƠN
        // =====================================================

        $items = DB::table('ChiTietDonHang')
            ->whereIn(
                'DonHangID',
                $orders->pluck('DonHangID')
            )

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


        // =====================================================
        // GẮN SẢN PHẨM VÀO TỪNG ĐƠN
        // =====================================================

        $orders->transform(function ($order) use ($items) {

            $order->items = $items->get(
                $order->DonHangID,
                collect()
            );

            return $order;
        });


        // =====================================================
        // TRẢ VỀ VIEW
        // =====================================================

        return view(
            'user.account.orders',
            compact(
                'user',
                'danhMucs',
                'orders'
            )
        );
    }


    // =========================================================
    // KHÁCH HỦY ĐƠN
    // Chỉ được hủy khi đơn vẫn còn là MoiTao
    // =========================================================

    public function cancel(
        OrderStatusService $service,
        $id
    ) {
        // Chưa đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = (int) session('NguoiDungID');


        // =====================================================
        // CHUYỂN:
        // MoiTao -> DaHuy
        //
        // OrderStatusService sẽ kiểm tra trạng thái thực tế
        // để tránh trường hợp Admin vừa xác nhận đơn.
        // =====================================================

        $ok = $service->change(
            (int) $id,
            'DaHuy',
            'khach_hang',
            'Khách hủy đơn',
            $nguoiDungID
        );


        // =====================================================
        // THÔNG BÁO
        // =====================================================

        return redirect()
            ->route('order.index')
            ->with(
                $ok ? 'success' : 'error',
                $ok
                    ? 'Đã hủy đơn hàng.'
                    : 'Không thể hủy đơn hàng này vì đơn đã được xác nhận hoặc không tồn tại.'
            );
    }


    // =========================================================
    // KHÁCH XÁC NHẬN ĐÃ NHẬN HÀNG
    //
    // DaGiao -> HoanThanh
    // =========================================================

    public function receive(
        OrderStatusService $service,
        $id
    ) {
        // Chưa đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()->route('login');
        }

        $nguoiDungID = (int) session('NguoiDungID');


        // =====================================================
        // CHUYỂN:
        // DaGiao -> HoanThanh
        // =====================================================

        $ok = $service->change(
            (int) $id,
            'HoanThanh',
            'khach_hang',
            'Khách xác nhận đã nhận hàng',
            $nguoiDungID
        );


        // =====================================================
        // THÔNG BÁO
        // =====================================================

        return redirect()
            ->route('order.index')
            ->with(
                $ok ? 'success' : 'error',
                $ok
                    ? 'Cảm ơn bạn! Đơn hàng đã hoàn tất, bạn có thể đánh giá sản phẩm.'
                    : 'Không thể xác nhận đơn hàng này.'
            );
    }


    



}