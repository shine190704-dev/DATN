<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Hiển thị danh sách và tìm kiếm thương hiệu.
     */
    public function index(Request $request)
    {
        $keyword = trim($request->input('search', ''));

        $brands = ThuongHieu::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('TenThuongHieu', 'like', "%{$keyword}%")
                        ->orWhere('MaThuongHieu', 'like', "%{$keyword}%");

                    if (ctype_digit($keyword)) {
                        $q->orWhere('ThuongHieuID', (int) $keyword);
                    }
                });
            })
            ->orderBy('ThuongHieuID', 'asc')
            ->get();

        return view('admin.brand', compact('brands', 'keyword'));
    }

    /**
     * Thêm thương hiệu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'TenThuongHieu' => [
                'required',
                'string',
                'max:150',
            ],
        ], [
            'TenThuongHieu.required' => 'Vui lòng nhập tên thương hiệu.',
            'TenThuongHieu.max' => 'Tên thương hiệu không được vượt quá 150 ký tự.',
        ]);

        $brandName = trim($validated['TenThuongHieu']);
        $codeBase = Str::limit(Str::slug($brandName), 50, '');
        $codeBase = $codeBase !== '' ? $codeBase : 'brand';
        $brandCode = $codeBase;
        $suffix = 2;

        while (ThuongHieu::query()->where('MaThuongHieu', $brandCode)->exists()) {
            $suffixText = "-{$suffix}";
            $brandCode = Str::limit($codeBase, 50 - strlen($suffixText), '') . $suffixText;
            $suffix++;
        }

        ThuongHieu::create([
            'TenThuongHieu' => $brandName,
            'MaThuongHieu' => $brandCode,
            'MoTa' => '',
            'TrangThai' => 'HoatDong',
            'NgayTao' => now(),
            'NgayCapNhat' => now(),
        ]);

        return redirect()
            ->route('admin.brand.index')
            ->with('success', 'Thêm thương hiệu thành công.');
    }

    /**
     * Cập nhật thương hiệu.
     */
    public function update(Request $request, int $id)
    {
        $brand = ThuongHieu::findOrFail($id);

        $validated = $request->validate([
            'TenThuongHieu' => [
                'required',
                'string',
                'max:150',
            ],
        ], [
            'TenThuongHieu.required' => 'Vui lòng nhập tên thương hiệu.',
            'TenThuongHieu.max' => 'Tên thương hiệu không được vượt quá 150 ký tự.',
        ]);

        $brand->update([
            'TenThuongHieu' => trim($validated['TenThuongHieu']),
            'NgayCapNhat' => now(),
        ]);

        return redirect()
            ->route('admin.brand.index')
            ->with('success', 'Cập nhật thương hiệu thành công.');
    }

    /**
     * Xóa thương hiệu nếu chưa có sản phẩm sử dụng.
     */
    public function destroy(int $id)
    {
        $brand = ThuongHieu::findOrFail($id);

        $hasProducts = \App\Models\SanPham::query()
            ->where('ThuongHieuID', $id)
            ->exists();

        if ($hasProducts) {
            return redirect()
                ->route('admin.brand.index')
                ->with('error', 'Thương hiệu đang có sản phẩm, không thể xóa.');
        }

        $brand->delete();

        return redirect()
            ->route('admin.brand.index')
            ->with('success', 'Xóa thương hiệu thành công.');
    }
}
