<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    /**
     * Chuyển trạng thái đơn hàng
     */
 public function change(
    int $donHangID,
    string $newStatus,
    string $actor,
    ?string $note = null,
    ?int $nguoiDungID = null
): bool {

        return DB::transaction(function () use (
            $donHangID,
            $newStatus,
            $actor,
            $note,
            $nguoiDungID
        ) {

            // =====================================================
            // LẤY ĐƠN HÀNG VÀ KHÓA DÒNG
            // =====================================================

            $order = DB::table('DonHang')
                ->where('DonHangID', $donHangID)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return false;
            }


            // =====================================================
            // KHÁCH HÀNG
            // =====================================================

            if ($actor === 'khach_hang') {

                // Đơn phải thuộc về khách đang đăng nhập
                if (
                    $nguoiDungID === null ||
                    (int) $order->NguoiDungID !== $nguoiDungID
                ) {
                    return false;
                }


                // =================================================
                // KHÁCH HỦY ĐƠN
                // Chỉ được hủy đơn mới
                // =================================================

                if ($newStatus === 'DaHuy') {

                    if (!in_array($order->TrangThai, [
                        'MoiTao',
                        'ChoXacNhan',
                        'Chờ xác nhận',
                    ])) {
                        return false;
                    }
                }


                // =================================================
                // KHÁCH XÁC NHẬN ĐÃ NHẬN HÀNG
                // DaGiao -> HoanThanh
                // =================================================

                if ($newStatus === 'HoanThanh') {

                    if (!in_array($order->TrangThai, [
                        'DangGiao',
                        'DangGiaoHang',
                        'Đang giao hàng',
                    ])) {
                     return false;
                    }
                }


            // =====================================================
            // CẬP NHẬT TRẠNG THÁI
            // =====================================================

            DB::table('DonHang')
                ->where('DonHangID', $donHangID)
                ->update([
                    'TrangThai'   => $newStatus,
                    'NgayCapNhat' => now(),
                ]);


            // =====================================================
            // GHI NHẬT KÝ HỆ THỐNG
            // =====================================================

            if (DB::getSchemaBuilder()->hasTable('NhatKyHeThong')) {

                DB::table('NhatKyHeThong')->insert([
                    'PhanHe'         => 'DonHang',

                    'HanhDong'       => $newStatus,

                    'DoiTuong'      => 'DonHangID: ' . $donHangID,

                    'MoTa'           => $note
                        ?? 'Thay đổi trạng thái đơn hàng',

                    'NgayTao'       => now(),

                    'NguoiThaoTacID' => $nguoiDungID,
                ]);
            }


            return true;
        });
    }
}