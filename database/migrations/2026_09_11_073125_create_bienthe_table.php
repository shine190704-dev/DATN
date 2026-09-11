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
        Schema::create('bienthe', function (Blueprint $table) {
            $table->increments('BienTheID');
            $table->string('MauSac', 50);
            $table->string('KichThuoc', 50);
            $table->decimal('GiaBienThe', 12, 0);
            $table->unsignedInteger('SoLuong')->default(0);
            $table->unsignedInteger('SoLuongTamGiu')->nullable()->default(0);
            $table->unsignedInteger('SanPhamID')->index('sanphamid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bienthe');
    }
};
