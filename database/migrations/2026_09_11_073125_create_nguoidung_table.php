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
        Schema::create('nguoidung', function (Blueprint $table) {
            $table->increments('NguoiDungID');
            $table->string('Ho', 100);
            $table->string('Ten', 50);
            $table->string('Email', 100)->unique('email');
            $table->string('SoDienThoai', 10)->unique('sodienthoai');
            $table->string('MatKhau');
            $table->string('VaiTro', 30)->default('KhachHang');
            $table->string('TrangThai', 30)->nullable()->default('HoatDong');
            $table->string('MaDatLaiMatKhau', 225)->nullable();
            $table->dateTime('ThoiGianHetHanMaDatLaiMatKhau')->nullable();
            $table->dateTime('NgayTao')->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoidung');
    }
};
