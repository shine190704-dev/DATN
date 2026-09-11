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
        Schema::create('chitietdonhang', function (Blueprint $table) {
            $table->increments('ChiTietDonHangID');
            $table->string('TenSanPham', 200);
            $table->string('MauSac', 50);
            $table->string('HinhAnh', 500);
            $table->string('KichThuoc', 50);
            $table->unsignedInteger('SoLuong');
            $table->decimal('GiaTaiThoiDiemMua', 12, 0);
            $table->unsignedInteger('DonHangID')->index('donhangid');
            $table->unsignedInteger('SanPhamID')->index('sanphamid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietdonhang');
    }
};
