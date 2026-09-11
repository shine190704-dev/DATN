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
        Schema::create('danhgia', function (Blueprint $table) {
            $table->increments('DanhGiaID');
            $table->unsignedTinyInteger('DiemDanhGia');
            $table->text('BinhLuan');
            $table->string('TrangThai', 30)->nullable()->default('HienThi');
            $table->dateTime('NgayTao')->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->unsignedInteger('NguoiDungID')->index('nguoidungid');
            $table->unsignedInteger('SanPhamID')->index('sanphamid');
            $table->unsignedInteger('DonHangID');

            $table->unique(['DonHangID', 'SanPhamID'], 'uq_danhgia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danhgia');
    }
};
