<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OrderStatusService
{
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
            $order = DB::table('DonHang')
                ->where('DonHangID', $donHangID)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return false;
            }

            if ($actor === 'khach_hang') {
                if (
                    $nguoiDungID === null ||
                    (int) $order->NguoiDungID !== $nguoiDungID
                ) {
                    return false;
                }

                if ($newStatus === 'DaHuy' && !in_array($order->TrangThai, [
                    'MoiTao',
                    'ChoXacNhan',
                    'Chờ xác nhận',
                ], true)) {
                    return false;
                }

                if ($newStatus === 'HoanThanh' && !in_array($order->TrangThai, [
                    'DaGiao',
                    'Đã giao',
                    'DangGiao',
                    'DangGiaoHang',
                    'Đang giao hàng',
                ], true)) {
                    return false;
                }
            }

            DB::table('DonHang')
                ->where('DonHangID', $donHangID)
                ->update([
                    'TrangThai' => $newStatus,
                    'NgayCapNhat' => now(),
                ]);

            if (DB::getSchemaBuilder()->hasTable('NhatKyHeThong')) {
                DB::table('NhatKyHeThong')->insert([
                    'PhanHe' => 'DonHang',
                    'HanhDong' => $newStatus,
                    'DoiTuong' => 'DonHangID: ' . $donHangID,
                    'MoTa' => $note ?? 'Thay đổi trạng thái đơn hàng',
                    'NgayTao' => now(),
                    'NguoiThaoTacID' => $nguoiDungID,
                ]);
            }

            return true;
        });
    }
}
