<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // =========================================
    // LẤY DANH MỤC CHO NAVBAR
    // =========================================
    private function getDanhMucs()
    {
        return DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();
    }


    // =========================================
    // KHÁM PHÁ TẤT CẢ
    // =========================================
    public function all()
{
    $danhMucs = $this->getDanhMucs();

    $query = DB::table('SanPham')
        ->where('SanPham.TrangThai', 'HoatDong')
        ->select('SanPham.*')
        ->selectSub($this->subQueryHinhAnh(), 'HinhAnh');

    $products = $this->applySort($query)->get();

    return view(
        'user.products.product-all',
        compact('danhMucs', 'products')
    );
}


    // =========================================
// SẢN PHẨM MỚI - TRONG VÒNG 30 NGÀY
// =========================================
public function newProducts()
{
    $danhMucs = $this->getDanhMucs();

    $query = DB::table('SanPham')
        ->where('SanPham.TrangThai', 'HoatDong')

        // Chỉ lấy sản phẩm được tạo trong 30 ngày gần nhất
        ->where(
            'SanPham.NgayTao',
            '>',
            now()->subDays(30)
        )

        ->select('SanPham.*')

        // Lấy ảnh đại diện
        ->selectSub(
            $this->subQueryHinhAnh(),
            'HinhAnh'
        )

        ;

    $products = $this->applySort($query)->get();

    return view(
        'user.products.product-new',
        compact(
            'danhMucs',
            'products'
        )
    );
}


    // =========================================
    // DANH MỤC
    // =========================================
    public function show($id)
{
    $danhMucs = $this->getDanhMucs();

    // Lấy danh mục đang được chọn
    $danhMuc = DB::table('danhmuc')
        ->where('DanhMucID', $id)
        ->where('TrangThai', 'HoatDong')
        ->first();

    if (!$danhMuc) {
        abort(404);
    }

    // Lấy sản phẩm thuộc danh mục
    $query = DB::table('SanPham')
        ->where('SanPham.DanhMucID', $id)
        ->where('SanPham.TrangThai', 'HoatDong')
        ->select('SanPham.*')
        ->selectSub(
            $this->subQueryHinhAnh(),
            'HinhAnh'
        );

    // ================================
    // SẮP XẾP
    // ================================
    $sort = request('sort', 'newest');

    switch ($sort) {

        // MỚI NHẤT
        case 'newest':
            $query->orderByDesc('SanPham.NgayTao')
                  ->orderByDesc('SanPham.SanPhamID');
            break;

        // A - Z
        case 'az':
            $query->orderBy('SanPham.TenSanPham', 'asc');
            break;

        // Z - A
        case 'za':
            $query->orderBy('SanPham.TenSanPham', 'desc');
            break;

        // GIÁ THẤP → CAO
        case 'price_asc':
            $query->orderBy('SanPham.Gia', 'asc');
            break;

        // GIÁ CAO → THẤP
        case 'price_desc':
            $query->orderBy('SanPham.Gia', 'desc');
            break;

        default:
            $query->orderByDesc('SanPham.NgayTao')
                  ->orderByDesc('SanPham.SanPhamID');
            break;
    }

    $products = $query->get();

    return view(
        'user.products.product-category',
        compact(
            'danhMucs',
            'danhMuc',
            'products'
        )
    );
}



    private function applySort($query)
{
    $sort = request('sort', 'newest');

    switch ($sort) {

    case 'newest':
        $query->orderByDesc('SanPham.NgayTao')
              ->orderByDesc('SanPham.SanPhamID');
        break;

    case 'az':
        $query->orderBy('SanPham.TenSanPham', 'asc')
              ->orderByDesc('SanPham.NgayTao')
              ->orderByDesc('SanPham.SanPhamID');
        break;

    case 'za':
        $query->orderBy('SanPham.TenSanPham', 'desc')
              ->orderByDesc('SanPham.NgayTao')
              ->orderByDesc('SanPham.SanPhamID');
        break;

    case 'price_asc':
        $query->orderBy('SanPham.Gia', 'asc')
              ->orderByDesc('SanPham.NgayTao')
              ->orderByDesc('SanPham.SanPhamID');
        break;

    case 'price_desc':
        $query->orderBy('SanPham.Gia', 'desc')
              ->orderByDesc('SanPham.NgayTao')
              ->orderByDesc('SanPham.SanPhamID');
        break;

    default:
        $query->orderByDesc('SanPham.NgayTao')
              ->orderByDesc('SanPham.SanPhamID');
        break;
}

    return $query;
}


    // =========================================
    // LẤY ẢNH ĐẠI DIỆN SẢN PHẨM
    // =========================================
    private function subQueryHinhAnh()
    {
        return function ($query) {

            $query->from('HinhAnhSanPham')
                ->select('DuongDanAnh')

                ->whereColumn(
                    'HinhAnhSanPham.SanPhamID',
                    'SanPham.SanPhamID'
                )

                ->orderByDesc('AnhDaiDien')
                ->orderBy('HinhAnhSanPhamID')

                ->limit(1);
        };
    }




    public function search()
{
    $danhMucs = $this->getDanhMucs();

    $keyword = trim(request('keyword', ''));

    $products = collect();

    if ($keyword !== '') {

        $products = DB::table('SanPham')
            ->where('SanPham.TrangThai', 'HoatDong')

            ->where(function ($query) use ($keyword) {

                $query->where(
                    'SanPham.TenSanPham',
                    'like',
                    '%' . $keyword . '%'
                )

                ->orWhere(
                    'SanPham.MoTa',
                    'like',
                    '%' . $keyword . '%'
                )

                ->orWhere(
                    'SanPham.MaSKU',
                    'like',
                    '%' . $keyword . '%'
                );

            })

            ->orderByDesc('SanPham.NgayTao')
            ->orderByDesc('SanPham.SanPhamID')

            ->select('SanPham.*')

            ->selectSub(
                $this->subQueryHinhAnh(),
                'HinhAnh'
            )

            ->get();
    }

    return view(
        'user.products.product-search',
        compact(
            'danhMucs',
            'products',
            'keyword'
        )
    );
}


    


}