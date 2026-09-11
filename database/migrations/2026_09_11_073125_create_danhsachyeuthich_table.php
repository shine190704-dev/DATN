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
        Schema::create('danhsachyeuthich', function (Blueprint $table) {
            $table->increments('DanhSachYeuThichID');
            $table->dateTime('NgayTao')->nullable()->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->unsignedInteger('NguoiDungID');
            $table->unsignedInteger('SanPhamID')->index('sanphamid');

            $table->unique(['NguoiDungID', 'SanPhamID'], 'uq_yeu_thich');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danhsachyeuthich');
    }
};
