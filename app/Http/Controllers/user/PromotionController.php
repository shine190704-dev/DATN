<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CapMaGiamGia;
use App\Models\DanhMuc;
use App\Models\MaGiamGia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Hiển thị danh sách mã khuyến mãi cho người dùng.
     */
    public function index()
    {
        $now = now();
        $danhMucs = DanhMuc::query()
            ->where('TrangThai', 'HoatDong')
            ->get();

        // Hiển thị mã đang hoạt động và chưa hết hạn,
        // bao gồm cả mã chưa đến ngày bắt đầu.
        $promotions = MaGiamGia::query()
            ->where('TrangThai', 'HoatDong')
            ->where('NgayHetHan', '>=', $now->copy()->startOfDay())
            ->orderBy('NgayBatDau')
            ->get();

        // Lấy các mã khách hàng hiện tại đã nhận.
        $claimedPromotionIds = [];

        if (session()->has('NguoiDungID')) {
            $claimedPromotionIds = CapMaGiamGia::query()
                ->where('NguoiDungID', session('NguoiDungID'))
                ->pluck('MaGiamGiaID')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return view('user.promotion.index', compact(
            'danhMucs',
            'promotions',
            'claimedPromotionIds'
        ));
    }

    /**
     * Khách hàng nhận mã khuyến mãi.
     */
    public function claim(int $id)
    {
        $userId = session('NguoiDungID');

        if (!$userId) {
            return back()->with(
                'error',
                'Vui lòng đăng nhập để nhận mã giảm giá.'
            );
        }

        $result = DB::transaction(function () use ($id, $userId) {
            // Khóa bản ghi để hạn chế hai người nhận vượt số lượng
            // khi cùng nhận mã vào một thời điểm.
            $promotion = MaGiamGia::query()
                ->where('MaGiamGiaID', $id)
                ->lockForUpdate()
                ->first();

            if (!$promotion || $promotion->TrangThai !== 'HoatDong') {
                return [
                    'success' => false,
                    'message' => 'Mã giảm giá không tồn tại hoặc đã tạm dừng.',
                ];
            }

            $now = now();

            $start = Carbon::parse($promotion->NgayBatDau)->startOfDay();
            $end = Carbon::parse($promotion->NgayHetHan)->endOfDay();

            if ($now->lt($start)) {
                return [
                    'success' => false,
                    'message' => 'Mã giảm giá chưa đến ngày nhận.',
                ];
            }

            if ($now->gt($end)) {
                return [
                    'success' => false,
                    'message' => 'Mã giảm giá đã hết hạn.',
                ];
            }

            // Kiểm tra khách hàng đã nhận mã này chưa.
            $alreadyClaimed = CapMaGiamGia::query()
                ->where('NguoiDungID', $userId)
                ->where('MaGiamGiaID', $id)
                ->exists();

            if ($alreadyClaimed) {
                return [
                    'success' => false,
                    'message' => 'Em đã nhận mã giảm giá này rồi.',
                ];
            }

            $issued = (int) $promotion->SoLuongDaCap;
            $quantity = (int) $promotion->SoLuongPhatHanh;

            if ($issued >= $quantity) {
                return [
                    'success' => false,
                    'message' => 'Mã giảm giá đã hết lượt nhận.',
                ];
            }

            CapMaGiamGia::create([
                'MaGiamGiaID' => $promotion->MaGiamGiaID,
                'NguoiDungID' => $userId,
                'DaSuDung' => 0,
                'NgayCap' => $now,
                'NgaySuDung' => null,
            ]);

            // Cập nhật số lượng đã cấp.
            $promotion->SoLuongDaCap = $issued + 1;
            $promotion->save();

            return [
                'success' => true,
                'message' => 'Nhận mã giảm giá thành công!',
            ];
        });

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }
}