<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HIỂN THỊ GIỎ HÀNG
    |--------------------------------------------------------------------------
    */
public function index()
{
    if (!session()->has('NguoiDungID')) {
        return redirect()
            ->route('login')
            ->with('error', 'Vui lòng đăng nhập để xem giỏ hàng.');
    }

    $nguoiDungID = session('NguoiDungID');

    // Lấy danh mục cho navbar
    $danhMucs = DB::table('danhmuc')
        ->where('TrangThai', 'HoatDong')
        ->get();

    // Tìm giỏ hàng
    $gioHang = DB::table('GioHang')
        ->where('NguoiDungID', $nguoiDungID)
        ->first();

    // Chưa có giỏ hàng
    if (!$gioHang) {
        return view(
            'user.products.cart',
            [
                'items' => collect(),
                'total' => 0,
                'danhMucs' => $danhMucs
            ]
        );
    }

    // Lấy sản phẩm trong giỏ
    $items = DB::table('ChiTietGioHang')
        ->join(
            'BienThe',
            'ChiTietGioHang.BienTheID',
            '=',
            'BienThe.BienTheID'
        )
        ->join(
            'SanPham',
            'BienThe.SanPhamID',
            '=',
            'SanPham.SanPhamID'
        )
        ->leftJoin(
            'HinhAnhSanPham',
            function ($join) {
                $join->on(
                    'SanPham.SanPhamID',
                    '=',
                    'HinhAnhSanPham.SanPhamID'
                )
                ->where(
                    'HinhAnhSanPham.AnhDaiDien',
                    1
                );
            }
        )
        ->where(
            'ChiTietGioHang.GioHangID',
            $gioHang->GioHangID
        )
        ->where(
            'SanPham.TrangThai',
            'HoatDong'
        )
        ->select(
            'ChiTietGioHang.ChiTietGioHangID',
            'ChiTietGioHang.BienTheID',
            'ChiTietGioHang.SoLuong',

            'SanPham.SanPhamID',
            'SanPham.TenSanPham',

            'BienThe.MauSac',
            'BienThe.KichThuoc',
            'BienThe.GiaBienThe',

            'HinhAnhSanPham.DuongDanAnh as HinhAnh'
        )
        ->get();

    // Tính thành tiền
    $total = 0;

    foreach ($items as $item) {
        $item->ThanhTien =
            $item->SoLuong * $item->GiaBienThe;

        $total += $item->ThanhTien;
    }

    return view(
        'user.products.cart',
        compact(
            'items',
            'total',
            'danhMucs'
        )
    );
}

    /*
    |--------------------------------------------------------------------------
    | THÊM SẢN PHẨM VÀO GIỎ
    |--------------------------------------------------------------------------
    */
    public function add(Request $request)
    {
        // Kiểm tra đăng nhập
        if (!session()->has('NguoiDungID')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng.'
            ], 401);
        }

        // Kiểm tra dữ liệu gửi lên
        $request->validate([
            'BienTheID' => 'required|integer',
            'SoLuong' => 'required|integer|min:1'
        ]);

        $nguoiDungID = session('NguoiDungID');

        $bienTheID = $request->BienTheID;
        $soLuong = $request->SoLuong;


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA BIẾN THỂ
        |--------------------------------------------------------------------------
        */

        $bienThe = DB::table('BienThe')
            ->join(
                'SanPham',
                'BienThe.SanPhamID',
                '=',
                'SanPham.SanPhamID'
            )
            ->where(
                'BienThe.BienTheID',
                $bienTheID
            )
            ->where(
                'SanPham.TrangThai',
                'HoatDong'
            )
            ->select(
                'BienThe.*',
                'SanPham.TenSanPham'
            )
            ->first();

        if (!$bienThe) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại.'
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA TỒN KHO
        |--------------------------------------------------------------------------
        */

        if ($soLuong > $bienThe->SoLuong) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng sản phẩm không đủ trong kho.'
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | TÌM / TẠO GIỎ HÀNG
        |--------------------------------------------------------------------------
        */

        $gioHang = DB::table('GioHang')
            ->where(
                'NguoiDungID',
                $nguoiDungID
            )
            ->first();

        if (!$gioHang) {

            $gioHangID = DB::table('GioHang')
                ->insertGetId([
                    'NguoiDungID' => $nguoiDungID,
                    'NgayTao' => now(),
                    'NgayCapNhat' => now()
                ]);

        } else {

            $gioHangID = $gioHang->GioHangID;

        }


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA SẢN PHẨM ĐÃ CÓ TRONG GIỎ CHƯA
        |--------------------------------------------------------------------------
        */

        $item = DB::table('ChiTietGioHang')
            ->where(
                'GioHangID',
                $gioHangID
            )
            ->where(
                'BienTheID',
                $bienTheID
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | NẾU ĐÃ CÓ → CỘNG SỐ LƯỢNG
        |--------------------------------------------------------------------------
        */

        if ($item) {

            $soLuongMoi =
                $item->SoLuong + $soLuong;

            if ($soLuongMoi > $bienThe->SoLuong) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng sản phẩm trong kho không đủ.'
                ], 422);
            }

            DB::table('ChiTietGioHang')
                ->where(
                    'ChiTietGioHangID',
                    $item->ChiTietGioHangID
                )
                ->update([
                    'SoLuong' => $soLuongMoi
                ]);

        } else {

            /*
            |--------------------------------------------------------------------------
            | CHƯA CÓ → THÊM DÒNG MỚI
            |--------------------------------------------------------------------------
            */

            DB::table('ChiTietGioHang')
                ->insert([
                    'GioHangID' => $gioHangID,
                    'BienTheID' => $bienTheID,
                    'SoLuong' => $soLuong
                ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng.',
            'cartCount' => $this->getCartCount($nguoiDungID)
        ]);
    }

    private function getCartCount($nguoiDungID)
    {
        return (int) DB::table('ChiTietGioHang')
            ->join(
                'GioHang',
                'ChiTietGioHang.GioHangID',
                '=',
                'GioHang.GioHangID'
            )
            ->where('GioHang.NguoiDungID', $nguoiDungID)
            ->sum('ChiTietGioHang.SoLuong');
    }


    /*
    |--------------------------------------------------------------------------
    | CẬP NHẬT SỐ LƯỢNG
    |--------------------------------------------------------------------------
    */
    public function update(Request $request)
    {
        if (!session()->has('NguoiDungID')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập.'
            ], 401);
        }

        $request->validate([
            'ChiTietGioHangID' => 'required|integer',
            'SoLuong' => 'required|integer|min:1'
        ]);

        $nguoiDungID = session('NguoiDungID');

        $item = DB::table('ChiTietGioHang')
            ->join(
                'GioHang',
                'ChiTietGioHang.GioHangID',
                '=',
                'GioHang.GioHangID'
            )
            ->join(
                'BienThe',
                'ChiTietGioHang.BienTheID',
                '=',
                'BienThe.BienTheID'
            )
            ->where(
                'ChiTietGioHang.ChiTietGioHangID',
                $request->ChiTietGioHangID
            )
            ->where(
                'GioHang.NguoiDungID',
                $nguoiDungID
            )
            ->select(
                'ChiTietGioHang.*',
                'BienThe.SoLuong as TonKho'
            )
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ.'
            ], 404);
        }

        if ($request->SoLuong > $item->TonKho) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng vượt quá số lượng trong kho.'
            ], 422);
        }

        DB::table('ChiTietGioHang')
            ->where(
                'ChiTietGioHangID',
                $request->ChiTietGioHangID
            )
            ->update([
                'SoLuong' => $request->SoLuong
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật số lượng.',
            'cartCount' => $this->getCartCount($nguoiDungID)
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | XÓA SẢN PHẨM KHỎI GIỎ
    |--------------------------------------------------------------------------
    */
    public function remove(Request $request)
    {
        if (!session()->has('NguoiDungID')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập.'
            ], 401);
        }

        $request->validate([
            'ChiTietGioHangID' => 'required|integer'
        ]);

        $nguoiDungID = session('NguoiDungID');

        $item = DB::table('ChiTietGioHang')
            ->join(
                'GioHang',
                'ChiTietGioHang.GioHangID',
                '=',
                'GioHang.GioHangID'
            )
            ->where(
                'ChiTietGioHang.ChiTietGioHangID',
                $request->ChiTietGioHangID
            )
            ->where(
                'GioHang.NguoiDungID',
                $nguoiDungID
            )
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ.'
            ], 404);
        }

        DB::table('ChiTietGioHang')
            ->where(
                'ChiTietGioHangID',
                $request->ChiTietGioHangID
            )
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'cartCount' => $this->getCartCount($nguoiDungID)
        ]);
    }
}