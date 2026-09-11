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
        Schema::create('nhacungcap', function (Blueprint $table) {
            $table->increments('NhaCungCapID');
            $table->string('TenNhaCungCap', 200);
            $table->string('SoDienThoai', 15)->unique('sodienthoai');
            $table->string('Email', 150)->unique('email');
            $table->string('DiaChi', 225);
            $table->string('TrangThai', 30)->nullable()->default('HoatDong');
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhacungcap');
    }
};
