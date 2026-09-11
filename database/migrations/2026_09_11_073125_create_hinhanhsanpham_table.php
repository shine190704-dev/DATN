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
        Schema::create('hinhanhsanpham', function (Blueprint $table) {
            $table->increments('HinhAnhSanPhamID');
            $table->string('DuongDanAnh', 500);
            $table->boolean('AnhDaiDien')->default(false);
            $table->dateTime('NgayTao')->nullable()->useCurrent();
            $table->unsignedInteger('SanPhamID')->index('sanphamid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hinhanhsanpham');
    }
};
