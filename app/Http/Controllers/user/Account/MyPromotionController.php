<?php

namespace App\Http\Controllers\User\Account;

use App\Http\Controllers\Controller;
use App\Models\CapMaGiamGia;
use App\Models\DanhMuc;
use App\Models\NguoiDung;
use Illuminate\Http\Request;


class MyPromotionController extends Controller
{
    /**
     * Hiển thị các mã giảm giá khách hàng đã nhận.
     */
    public function index(Request $request)
    {
        $userId = session('NguoiDungID');

        // Chỉ cho phép người dùng đã đăng nhập xem mã của mình.
        abort_unless(
            $userId,
            401,
            'Vui lòng đăng nhập để xem mã khuyến mãi của bạn.'
        );

        $user = NguoiDung::query()
            ->where('NguoiDungID', $userId)
            ->first();

        if (!$user) {
            session()->forget('NguoiDungID');

            return redirect()->route('login');
        }

        $filter = $request->query('filter', 'all');
        $now = now();

        // Lấy mã đã nhận và thông tin mã giảm giá tương ứng.
        $query = CapMaGiamGia::query()
            ->join(
                'magiamgia',
                'capmagiamgia.MaGiamGiaID',
                '=',
                'magiamgia.MaGiamGiaID'
            )
            ->where('capmagiamgia.NguoiDungID', $userId)
            ->select([
                'capmagiamgia.CapMaGiamGiaID',
                'capmagiamgia.MaGiamGiaID',
                'capmagiamgia.DaSuDung',
                'capmagiamgia.NgayCap',
                'capmagiamgia.NgaySuDung',
                'magiamgia.MaCode',
                'magiamgia.GiaTriGiam',
                'magiamgia.GiaTriDonHangToiThieu',
                'magiamgia.NgayBatDau',
                'magiamgia.NgayHetHan',
            ]);

        // Lọc theo trạng thái.
        if ($filter === 'unused') {
            $query->where('capmagiamgia.DaSuDung', 0)
                ->where('magiamgia.NgayHetHan', '>=', $now);
        } elseif ($filter === 'used') {
            $query->where('capmagiamgia.DaSuDung', 1);
        } elseif ($filter === 'expired') {
            $query->where('capmagiamgia.DaSuDung', 0)
                ->where('magiamgia.NgayHetHan', '<', $now);
        } else {
            $filter = 'all';
        }

        $promotions = $query
            ->orderByDesc('capmagiamgia.NgayCap')
            ->get();

        // Đếm số lượng theo từng nhóm để hiển thị trên các nút lọc.
        $allClaims = CapMaGiamGia::query()
            ->join(
                'magiamgia',
                'capmagiamgia.MaGiamGiaID',
                '=',
                'magiamgia.MaGiamGiaID'
            )
            ->where('capmagiamgia.NguoiDungID', $userId)
            ->select([
                'capmagiamgia.DaSuDung',
                'magiamgia.NgayHetHan',
            ])
            ->get();

        $promotionCounts = [
            'all' => $allClaims->count(),
            'unused' => $allClaims->filter(
                fn ($item) => !$item->DaSuDung
                    && $item->NgayHetHan >= $now
            )->count(),
            'used' => $allClaims->filter(
                fn ($item) => (int) $item->DaSuDung === 1
            )->count(),
            'expired' => $allClaims->filter(
                fn ($item) => !$item->DaSuDung
                    && $item->NgayHetHan < $now
            )->count(),
        ];
        $danhMucs = DanhMuc::query()
            ->where('TrangThai', 'HoatDong')
            ->get();

        return view('user.account.my-promotions', compact(
            'promotions',
            'promotionCounts',
            'filter',
            'danhMucs',
            'user'
        ));
    }
}