<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->input('search', ''));

        $categories = DanhMuc::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('TenDanhMuc', 'like', "%{$keyword}%");

                    if (ctype_digit($keyword)) {
                        $q->orWhere('DanhMucID', (int) $keyword);
                    }
                });
            })
            ->orderBy('DanhMucID', 'asc')
            ->get();

        return view('admin.category', compact('categories', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'TenDanhMuc' => [
                'required',
                'string',
                'max:100',
                'unique:danhmuc,TenDanhMuc',
            ],
        ], [
            'TenDanhMuc.required' => 'Vui lòng nhập tên danh mục.',
            'TenDanhMuc.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'TenDanhMuc.unique' => 'Tên danh mục đã tồn tại.',
        ]);

        DanhMuc::create([
            'TenDanhMuc' => trim($validated['TenDanhMuc']),
            'TrangThai' => 'HoatDong',
            'NgayTao' => now(),
            'NgayCapNhat' => now(),
        ]);

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Thêm danh mục thành công.');
    }

    public function update(Request $request, int $id)
    {
        $category = DanhMuc::findOrFail($id);

        $validated = $request->validate([
            'TenDanhMuc' => [
                'required',
                'string',
                'max:100',
                Rule::unique('danhmuc', 'TenDanhMuc')
                    ->ignore($category->DanhMucID, 'DanhMucID'),
            ],
        ], [
            'TenDanhMuc.required' => 'Vui lòng nhập tên danh mục.',
            'TenDanhMuc.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'TenDanhMuc.unique' => 'Tên danh mục đã tồn tại.',
        ]);

        $category->update([
            'TenDanhMuc' => trim($validated['TenDanhMuc']),
            'NgayCapNhat' => now(),
        ]);

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(int $id)
    {
        $category = DanhMuc::findOrFail($id);

        $hasProducts = SanPham::query()
            ->where('DanhMucID', $id)
            ->exists();

        if ($hasProducts) {
            return redirect()
                ->route('admin.category.index')
                ->with(
                    'error',
                    'Danh mục đang có sản phẩm, không thể xóa.'
                );
        }

        $category->delete();

        return redirect()
            ->route('admin.category.index')
            ->with('success', 'Xóa danh mục thành công.');
    }
}
