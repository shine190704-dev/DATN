<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaGiamGia;
use App\Models\CapMaGiamGia;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{

/**
 * Chỉ Admin được sử dụng chức năng quản lý mã khuyến mãi.
 */
private function authorizeAdmin()
{
    abort_unless(
        session('AdminVaiTro') === 'Admin',
        403,
        'Bạn không có quyền truy cập chức năng này.'
    );
}

    /**
     * Hiển thị danh sách mã khuyến mãi và tìm kiếm.
     */
    public function index(Request $request)
    {
        $keyword = trim($request->input('search', ''));

        $promotions = MaGiamGia::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where('MaCode', 'like', "%{$keyword}%");

                if (ctype_digit($keyword)) {
                    $query->orWhere('MaGiamGiaID', (int) $keyword);
                }
            })
            ->orderByDesc('MaGiamGiaID')
            ->get();

        // Đếm số lượt mã đã được cấp và số lượt đã sử dụng.
        $promotionStats = CapMaGiamGia::query()
            ->selectRaw('MaGiamGiaID, COUNT(*) as LuotNhan')
            ->selectRaw('SUM(CASE WHEN DaSuDung = 1 THEN 1 ELSE 0 END) as LuotDaDung')
            ->groupBy('MaGiamGiaID')
            ->get()
            ->keyBy('MaGiamGiaID');

        return view('admin.promotion', compact(
            'promotions',
            'promotionStats',
            'keyword'
        ));
    }

    /**
     * Thêm mã khuyến mãi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaCode' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                'unique:magiamgia,MaCode',
            ],
            'GiaTriGiam' => [
                'required',
                'numeric',
                'min:1',
                'max:100',
            ],
            'GiaTriDonHangToiThieu' => [
                'required',
                'numeric',
                'min:0',
            ],
            'NgayBatDau' => [
                'required',
                'date_format:Y-m-d',
            ],
            'NgayHetHan' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:NgayBatDau',
            ],
            'SoLuongPhatHanh' => [
                'required',
                'integer',
                'min:1',
            ],
            'TrangThai' => [
                'required',
                Rule::in(['HoatDong', 'TamAn', 'TamDung']),
            ],
        ], [
            'MaCode.required' => 'Vui lòng nhập mã khuyến mãi.',
            'MaCode.unique' => 'Mã khuyến mãi đã tồn tại.',
            'MaCode.regex' => 'Mã chỉ được chứa chữ cái, chữ số, dấu gạch ngang hoặc gạch dưới.',
            'GiaTriGiam.required' => 'Vui lòng nhập phần trăm giảm giá.',
            'GiaTriGiam.min' => 'Phần trăm giảm giá phải lớn hơn 0.',
            'GiaTriGiam.max' => 'Phần trăm giảm giá không được vượt quá 100%.',
            'GiaTriDonHangToiThieu.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
            'NgayBatDau.required' => 'Vui lòng chọn ngày bắt đầu.',
            'NgayHetHan.required' => 'Vui lòng chọn ngày kết thúc.',
            'NgayHetHan.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',
            'SoLuongPhatHanh.required' => 'Vui lòng nhập số lượng phát hành.',
            'SoLuongPhatHanh.min' => 'Số lượng phát hành phải lớn hơn 0.',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $validated['MaCode'] = strtoupper(trim($validated['MaCode']));
        $validated['NgayBatDau'] = Carbon::createFromFormat(
            'Y-m-d',
            $validated['NgayBatDau']
        )->startOfDay()->toDateTimeString();
        $validated['NgayHetHan'] = Carbon::createFromFormat(
            'Y-m-d',
            $validated['NgayHetHan']
        )->endOfDay()->toDateTimeString();
        $validated['LoaiGiamGia'] = 'PhanTram';
        $validated['SoLuongDaCap'] = 0;

        MaGiamGia::create($validated);

        return redirect()
            ->route('admin.promotion.index')
            ->with('success', 'Tạo mã khuyến mãi thành công.');
    }

    /**
     * Cập nhật mã khuyến mãi.
     */
    public function update(Request $request, int $id)
    {
        $promotion = MaGiamGia::findOrFail($id);

        $validated = $request->validate([
            'MaCode' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('magiamgia', 'MaCode')
                    ->ignore($promotion->MaGiamGiaID, 'MaGiamGiaID'),
            ],
            'GiaTriGiam' => [
                'required',
                'numeric',
                'min:1',
                'max:100',
            ],
            'GiaTriDonHangToiThieu' => [
                'required',
                'numeric',
                'min:0',
            ],
            'NgayBatDau' => [
                'required',
                'date_format:Y-m-d',
            ],
            'NgayHetHan' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:NgayBatDau',
            ],
            'SoLuongPhatHanh' => [
                'required',
                'integer',
                'min:1',
            ],
            'TrangThai' => [
                'required',
                Rule::in(['HoatDong', 'TamAn', 'TamDung']),
            ],
        ], [
            'MaCode.required' => 'Vui lòng nhập mã khuyến mãi.',
            'MaCode.unique' => 'Mã khuyến mãi đã tồn tại.',
            'MaCode.regex' => 'Mã chỉ được chứa chữ cái, chữ số, dấu gạch ngang hoặc gạch dưới.',
            'GiaTriGiam.min' => 'Phần trăm giảm giá phải lớn hơn 0.',
            'GiaTriGiam.max' => 'Phần trăm giảm giá không được vượt quá 100%.',
            'NgayHetHan.after_or_equal' => 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',
            'SoLuongPhatHanh.min' => 'Số lượng phát hành phải lớn hơn 0.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
        ]);

        // Không cho giảm số lượng phát hành thấp hơn số mã đã cấp.
        if (
            (int) $validated['SoLuongPhatHanh']
            < (int) $promotion->SoLuongDaCap
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'SoLuongPhatHanh' =>
                        'Số lượng phát hành không được nhỏ hơn số mã đã cấp.',
                ]);
        }

        $validated['MaCode'] = strtoupper(trim($validated['MaCode']));
        $validated['NgayBatDau'] = Carbon::createFromFormat(
            'Y-m-d',
            $validated['NgayBatDau']
        )->startOfDay()->toDateTimeString();
        $validated['NgayHetHan'] = Carbon::createFromFormat(
            'Y-m-d',
            $validated['NgayHetHan']
        )->endOfDay()->toDateTimeString();
        $validated['LoaiGiamGia'] = 'PhanTram';

        $promotion->update($validated);

        return redirect()
            ->route('admin.promotion.index')
            ->with('success', 'Cập nhật mã khuyến mãi thành công.');
    }

    /**
     * Xóa mã khuyến mãi chưa được cấp cho khách hàng.
     */
    public function destroy(int $id)
    {
        $promotion = MaGiamGia::findOrFail($id);

        $hasBeenIssued = CapMaGiamGia::where(
            'MaGiamGiaID',
            $promotion->MaGiamGiaID
        )->exists();

        if ($hasBeenIssued) {
            return redirect()
                ->route('admin.promotion.index')
                ->with(
                    'error',
                    'Mã đã được cấp cho khách hàng nên không thể xóa.'
                );
        }

        $promotion->delete();

        return redirect()
            ->route('admin.promotion.index')
            ->with('success', 'Xóa mã khuyến mãi thành công.');
    }
}
