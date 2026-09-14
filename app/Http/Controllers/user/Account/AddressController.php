<?php

namespace App\Http\Controllers\user\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Hiển thị sổ địa chỉ
     */
    public function index()
    {
        // Bắt buộc đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()
                ->route('login')
                ->with('error', 'Vui lòng đăng nhập để xem sổ địa chỉ.');
        }

        $nguoiDungID = session('NguoiDungID');

        // Lấy danh mục cho navbar
        $danhMucs = DB::table('danhmuc')
            ->where('TrangThai', 'HoatDong')
            ->get();

        // Chỉ lấy địa chỉ thuộc người dùng đang đăng nhập
        $addresses = DB::table('DiaChiNguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->orderByDesc('MacDinh')
            ->orderByDesc('DiaChiNguoiDungID')
            ->get();

        return view(
            'user.account.addresses',
            compact('addresses', 'danhMucs')
        );
    }


    /**
     * Thêm địa chỉ mới
     */
    public function store(Request $request)
    {
        // Bắt buộc đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()
                ->route('login')
                ->with('error', 'Vui lòng đăng nhập.');
        }

        $nguoiDungID = session('NguoiDungID');

        // Kiểm tra dữ liệu
        $validated = $request->validate([
            'TenNguoiNhan' => [
                'required',
                'string',
                'max:200'
            ],

            'SoDienThoai' => [
                'required',
                'string',
                'regex:/^0[0-9]{9}$/'
            ],

            'DiaChi' => [
                'required',
                'string',
                'max:225'
            ],

            'ThanhPho' => [
                'required',
                'string',
                'max:100'
            ],

            'MacDinh' => [
                'nullable',
                'boolean'
            ],
        ], [
            'TenNguoiNhan.required' => 'Vui lòng nhập tên người nhận.',

            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.regex' => 'Số điện thoại phải gồm 10 số và bắt đầu bằng số 0.',

            'DiaChi.required' => 'Vui lòng nhập địa chỉ.',

            'ThanhPho.required' => 'Vui lòng chọn tỉnh/thành phố.',
        ]);


        // Giới hạn tối đa 5 địa chỉ
        $addressCount = DB::table('DiaChiNguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->count();

        if ($addressCount >= 5) {
            return back()
                ->withInput()
                ->with('error', 'Bạn chỉ được lưu tối đa 5 địa chỉ.');
        }


        /*
        |--------------------------------------------------------------------------
        | Thêm địa chỉ
        |--------------------------------------------------------------------------
        |
        | Transaction đảm bảo:
        | - Nếu đây là địa chỉ đầu tiên -> mặc định
        | - Nếu chọn làm mặc định -> bỏ mặc định cũ trước
        | - Không xảy ra trường hợp 2 địa chỉ cùng mặc định
        |
        */

        DB::transaction(function () use (
            $validated,
            $nguoiDungID,
            $addressCount
        ) {

            // Nếu là địa chỉ đầu tiên thì bắt buộc là mặc định
            $isDefault = $addressCount === 0
                ? 1
                : (!empty($validated['MacDinh']) ? 1 : 0);


            // Nếu người dùng chọn địa chỉ mới làm mặc định
            if ($isDefault === 1) {
                DB::table('DiaChiNguoiDung')
                    ->where('NguoiDungID', $nguoiDungID)
                    ->update([
                        'MacDinh' => 0,
                        'NgayCapNhat' => now(),
                    ]);
            }


            // Thêm địa chỉ mới
            DB::table('DiaChiNguoiDung')->insert([
                'NguoiDungID' => $nguoiDungID,
                'TenNguoiNhan' => $validated['TenNguoiNhan'],
                'SoDienThoai' => $validated['SoDienThoai'],
                'DiaChi' => $validated['DiaChi'],
                'ThanhPho' => $validated['ThanhPho'],
                'MacDinh' => $isDefault,
                'NgayTao' => now(),
                'NgayCapNhat' => now(),
            ]);
        });


        return redirect()
            ->route('address.index')
            ->with('success', 'Thêm địa chỉ thành công.');
    }


    /**
     * Cập nhật địa chỉ
     */
    public function update(Request $request, $id)
    {
        // Bắt buộc đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()
                ->route('login')
                ->with('error', 'Vui lòng đăng nhập.');
        }

        $nguoiDungID = session('NguoiDungID');


        /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION
        |--------------------------------------------------------------------------
        |
        | Chỉ được sửa địa chỉ thuộc tài khoản đang đăng nhập.
        |
        */

        $address = DB::table('DiaChiNguoiDung')
            ->where('DiaChiNguoiDungID', $id)
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if (!$address) {
            abort(403, 'Bạn không có quyền chỉnh sửa địa chỉ này.');
        }


        // Validate
        $validated = $request->validate([
            'TenNguoiNhan' => [
                'required',
                'string',
                'max:200'
            ],

            'SoDienThoai' => [
                'required',
                'string',
                'regex:/^0[0-9]{9}$/'
            ],

            'DiaChi' => [
                'required',
                'string',
                'max:225'
            ],

            'ThanhPho' => [
                'required',
                'string',
                'max:100'
            ],

            'MacDinh' => [
                'nullable',
                'boolean'
            ],
        ], [
            'TenNguoiNhan.required' => 'Vui lòng nhập tên người nhận.',

            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.regex' => 'Số điện thoại phải gồm 10 số và bắt đầu bằng số 0.',

            'DiaChi.required' => 'Vui lòng nhập địa chỉ.',

            'ThanhPho.required' => 'Vui lòng chọn tỉnh/thành phố.',
        ]);


        DB::transaction(function () use (
            $validated,
            $nguoiDungID,
            $id,
            $address
        ) {

            /*
             * Nếu người dùng chọn địa chỉ này làm mặc định
             */
            if (!empty($validated['MacDinh'])) {

                // Gỡ mặc định của tất cả địa chỉ khác
                DB::table('DiaChiNguoiDung')
                    ->where('NguoiDungID', $nguoiDungID)
                    ->where('DiaChiNguoiDungID', '!=', $id)
                    ->update([
                        'MacDinh' => 0,
                        'NgayCapNhat' => now(),
                    ]);

                // Đặt địa chỉ hiện tại làm mặc định
                DB::table('DiaChiNguoiDung')
                    ->where('DiaChiNguoiDungID', $id)
                    ->where('NguoiDungID', $nguoiDungID)
                    ->update([
                        'TenNguoiNhan' => $validated['TenNguoiNhan'],
                        'SoDienThoai' => $validated['SoDienThoai'],
                        'DiaChi' => $validated['DiaChi'],
                        'ThanhPho' => $validated['ThanhPho'],
                        'MacDinh' => 1,
                        'NgayCapNhat' => now(),
                    ]);

                return;
            }


            /*
             * Nếu đây đang là địa chỉ mặc định,
             * không cho phép bỏ mặc định để thành 0.
             *
             * Vì hệ thống yêu cầu mỗi người dùng
             * luôn phải có đúng 1 địa chỉ mặc định.
             */
            $isStillDefault = $address->MacDinh == 1 ? 1 : 0;

            DB::table('DiaChiNguoiDung')
                ->where('DiaChiNguoiDungID', $id)
                ->where('NguoiDungID', $nguoiDungID)
                ->update([
                    'TenNguoiNhan' => $validated['TenNguoiNhan'],
                    'SoDienThoai' => $validated['SoDienThoai'],
                    'DiaChi' => $validated['DiaChi'],
                    'ThanhPho' => $validated['ThanhPho'],
                    'MacDinh' => $isStillDefault,
                    'NgayCapNhat' => now(),
                ]);
        });


        return redirect()
            ->route('address.index')
            ->with('success', 'Cập nhật địa chỉ thành công.');
    }


    /**
     * Đặt địa chỉ làm mặc định
     */
    public function setDefault($id)
    {
        // Bắt buộc đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()
                ->route('login')
                ->with('error', 'Vui lòng đăng nhập.');
        }

        $nguoiDungID = session('NguoiDungID');


        /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION
        |--------------------------------------------------------------------------
        |
        | Chỉ được đặt địa chỉ thuộc tài khoản hiện tại.
        |
        */

        $address = DB::table('DiaChiNguoiDung')
            ->where('DiaChiNguoiDungID', $id)
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if (!$address) {
            abort(403, 'Bạn không có quyền sử dụng địa chỉ này.');
        }


        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        |
        | Bước 1: Gỡ mặc định cũ
        | Bước 2: Đặt mặc định mới
        |
        */

        DB::transaction(function () use ($nguoiDungID, $id) {

            // Gỡ địa chỉ mặc định cũ
            DB::table('DiaChiNguoiDung')
                ->where('NguoiDungID', $nguoiDungID)
                ->update([
                    'MacDinh' => 0,
                    'NgayCapNhat' => now(),
                ]);


            // Đặt địa chỉ mới làm mặc định
            DB::table('DiaChiNguoiDung')
                ->where('DiaChiNguoiDungID', $id)
                ->where('NguoiDungID', $nguoiDungID)
                ->update([
                    'MacDinh' => 1,
                    'NgayCapNhat' => now(),
                ]);
        });


        return redirect()
            ->route('address.index')
            ->with('success', 'Đã đặt địa chỉ mặc định.');
    }


    /**
     * Xóa địa chỉ
     */
    public function destroy($id)
    {
        // Bắt buộc đăng nhập
        if (!session()->has('NguoiDungID')) {
            return redirect()
                ->route('login')
                ->with('error', 'Vui lòng đăng nhập.');
        }

        $nguoiDungID = session('NguoiDungID');


        /*
        |--------------------------------------------------------------------------
        | AUTHORIZATION
        |--------------------------------------------------------------------------
        |
        | Chỉ được xóa địa chỉ thuộc tài khoản hiện tại.
        |
        */

        $address = DB::table('DiaChiNguoiDung')
            ->where('DiaChiNguoiDungID', $id)
            ->where('NguoiDungID', $nguoiDungID)
            ->first();

        if (!$address) {
            abort(403, 'Bạn không có quyền xóa địa chỉ này.');
        }


        // Không cho xóa địa chỉ cuối cùng
        $addressCount = DB::table('DiaChiNguoiDung')
            ->where('NguoiDungID', $nguoiDungID)
            ->count();

        if ($addressCount <= 1) {
            return back()
                ->with('error', 'Không thể xóa địa chỉ cuối cùng.');
        }


        DB::transaction(function () use (
            $address,
            $nguoiDungID,
            $id
        ) {

            // Xóa địa chỉ
            DB::table('DiaChiNguoiDung')
                ->where('DiaChiNguoiDungID', $id)
                ->where('NguoiDungID', $nguoiDungID)
                ->delete();


            /*
             * Nếu địa chỉ vừa xóa là mặc định,
             * chọn một địa chỉ khác làm mặc định.
             */
            if ($address->MacDinh == 1) {

                $newDefault = DB::table('DiaChiNguoiDung')
                    ->where('NguoiDungID', $nguoiDungID)
                    ->orderByDesc('DiaChiNguoiDungID')
                    ->first();

                if ($newDefault) {
                    DB::table('DiaChiNguoiDung')
                        ->where('DiaChiNguoiDungID', $newDefault->DiaChiNguoiDungID)
                        ->where('NguoiDungID', $nguoiDungID)
                        ->update([
                            'MacDinh' => 1,
                            'NgayCapNhat' => now(),
                        ]);
                }
            }
        });


        return redirect()
            ->route('address.index')
            ->with('success', 'Đã xóa địa chỉ.');
    }
}