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
        Schema::create('thuonghieu', function (Blueprint $table) {
            $table->increments('ThuongHieuID');
            $table->string('TenThuongHieu', 150);
            $table->string('MaThuongHieu', 50)->unique('mathuonghieu');
            $table->text('MoTa');
            $table->string('TrangThai', 30)->nullable()->default('HoatDong');
            $table->dateTime('NgayTao')->nullable()->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuonghieu');
    }
};
