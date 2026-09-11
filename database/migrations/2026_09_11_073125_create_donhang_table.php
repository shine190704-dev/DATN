<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donhang', function (Blueprint $table) {
            $table->increments('DonHangID');
            $table->string('MaDonHang', 50)->unique('madonhang');
            $table->decimal('TongTien', 12, 0);
            $table->string('TenNguoiNhan', 150);
            $table->string('SoDienThoaiNguoiNhan', 10);
            $table->string('DiaChiNhanHang', 225);
            $table->string('TrangThai', 30)->default('MoiTao');
            $table->string('PhuongThucThanhToan', 100);
            $table->string('TrangThaiThanhToan', 100)->default('ChuaThanhToan');
            $table->string('MaThanhToan', 100)->nullable();
            $table->string('MaGiaoDich', 100)->nullable();
            $table->dateTime('ThoiGianThanhToan')->nullable();
            $table->dateTime('ThoiHanThanhToan')->nullable();
            $table->string('MaVanDon', 100)->nullable()->unique('mavandon');
            $table->dateTime('NgayTao')->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->useCurrent();
            $table->unsignedInteger('NguoiDungID')->index('nguoidungid');
            $table->unsignedInteger('MaGiamGiaID')->nullable()->index('magiamgiaid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donhang');
    }
};
