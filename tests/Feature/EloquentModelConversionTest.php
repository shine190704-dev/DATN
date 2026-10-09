<?php

namespace Tests\Feature;

use App\Models\BienThe;
use App\Models\CapMaGiamGia;
use App\Models\ChiTietDonDatHang;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietGioHang;
use App\Models\DanhGia;
use App\Models\DanhMuc;
use App\Models\DanhSachYeuThich;
use App\Models\DiaChiNguoiDung;
use App\Models\DonDatHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\HinhAnhSanPham;
use App\Models\LichSuDonHang;
use App\Models\MaGiamGia;
use App\Models\NguoiDung;
use App\Models\NhatKyHeThong;
use App\Models\SanPham;
use App\Models\ThanhToanNhapHang;
use App\Models\ThuongHieu;
use App\Models\User;
use App\Models\YeuCauHoanTien;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EloquentModelConversionTest extends TestCase
{
    public function test_eloquent_model_reads_the_existing_legacy_table_and_key(): void
    {
        $modelMetadata = [
            BienThe::class => ['bienthe', 'BienTheID'],
            CapMaGiamGia::class => ['capmagiamgia', 'CapMaGiamGiaID'],
            DanhMuc::class => ['danhmuc', 'DanhMucID'],
            ChiTietDonDatHang::class => ['chitietdondathang', 'ChiTietDonDatHangID'],
            ChiTietDonHang::class => ['chitietdonhang', 'ChiTietDonHangID'],
            ChiTietGioHang::class => ['chitietgiohang', 'ChiTietGioHangID'],
            DanhGia::class => ['danhgia', 'DanhGiaID'],
            DanhSachYeuThich::class => ['danhsachyeuthich', 'DanhSachYeuThichID'],
            DiaChiNguoiDung::class => ['diachinguoidung', 'DiaChiNguoiDungID'],
            DonDatHang::class => ['dondathang', 'DonDatHangID'],
            DonHang::class => ['donhang', 'DonHangID'],
            GioHang::class => ['giohang', 'GioHangID'],
            HinhAnhSanPham::class => ['hinhanhsanpham', 'HinhAnhSanPhamID'],
            LichSuDonHang::class => ['lichsudonhang', 'LichSuDonHangID'],
            MaGiamGia::class => ['magiamgia', 'MaGiamGiaID'],
            NguoiDung::class => ['nguoidung', 'NguoiDungID'],
            NhatKyHeThong::class => ['nhatkyhethong', 'NhatKyHeThongID'],
            SanPham::class => ['sanpham', 'SanPhamID'],
            ThanhToanNhapHang::class => ['thanhtoannhaphang', 'ThanhToanNhapHangID'],
            ThuongHieu::class => ['thuonghieu', 'ThuongHieuID'],
            User::class => ['users', 'id'],
            YeuCauHoanTien::class => ['yeucauhoantien', 'YeuCauHoanTienID'],
        ];

        foreach ($modelMetadata as $modelClass => [$table, $primaryKey]) {
            $model = new $modelClass;

            $this->assertSame($table, $model->getTable());
            $this->assertSame($primaryKey, $model->getKeyName());
        }

        Schema::create('nguoidung', function (Blueprint $table): void {
            $table->increments('NguoiDungID');
            $table->string('Ho');
            $table->string('Ten');
            $table->string('Email');
            $table->string('SoDienThoai');
            $table->string('MatKhau');
            $table->string('VaiTro');
            $table->string('TrangThai');
        });

        NguoiDung::query()->from('NguoiDung')->insert([
            'Ho' => 'Nguyen',
            'Ten' => 'An',
            'Email' => 'an@example.com',
            'SoDienThoai' => '0900000000',
            'MatKhau' => 'hashed-password',
            'VaiTro' => 'KhachHang',
            'TrangThai' => 'HoatDong',
        ]);

        $user = NguoiDung::query()
            ->from('NguoiDung')
            ->where('Email', 'an@example.com')
            ->first();

        $this->assertInstanceOf(NguoiDung::class, $user);
        $this->assertSame(1, $user->getKey());
    }

    public function test_category_admin_page_renders_through_the_admin_layout(): void
    {
        Schema::create('danhmuc', function (Blueprint $table): void {
            $table->increments('DanhMucID');
            $table->string('TenDanhMuc');
            $table->string('TrangThai')->nullable();
            $table->dateTime('NgayTao')->nullable();
            $table->dateTime('NgayCapNhat')->nullable();
        });

        Schema::create('sanpham', function (Blueprint $table): void {
            $table->increments('SanPhamID');
            $table->unsignedInteger('DanhMucID')->nullable();
        });

        $this->withoutVite()
            ->get('/category')
            ->assertOk()
            ->assertSee('QUẢN LÝ DANH MỤC')
            ->assertSee('admin-sidebar', false);
    }
}
