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
        Schema::create('chitietdondathang', function (Blueprint $table) {
            $table->increments('ChiTietDonDatHangID');
            $table->string('MauSac', 50);
            $table->string('KichThuoc', 50);
            $table->unsignedInteger('SoLuongDat');
            $table->unsignedInteger('SoLuongDaNhan')->default(0);
            $table->decimal('GiaNhap', 14, 0);
            $table->decimal('ThanhTien', 14, 0);
            $table->text('GhiChu')->nullable();
            $table->unsignedInteger('DonDatHangID')->index('dondathangid');
            $table->unsignedInteger('SanPhamID')->index('sanphamid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietdondathang');
    }
};
