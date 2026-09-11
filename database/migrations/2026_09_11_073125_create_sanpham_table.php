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
        Schema::create('sanpham', function (Blueprint $table) {
            $table->increments('SanPhamID');
            $table->string('TenSanPham', 200);
            $table->text('MoTa')->nullable();
            $table->decimal('Gia', 12, 0);
            $table->string('ChatLieu', 100)->nullable();
            $table->string('BoLoc', 100)->nullable();
            $table->string('MaSKU', 60)->unique('masku');
            $table->boolean('NoiBat')->nullable()->default(false);
            $table->unsignedInteger('DaBan')->nullable()->default(0);
            $table->string('TrangThai', 30)->default('HoatDong');
            $table->unsignedInteger('LuongTonKhoThap')->default(5);
            $table->dateTime('NgayTao')->nullable()->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->unsignedInteger('NhaCungCapID')->index('nhacungcapid');
            $table->unsignedInteger('DanhMucID')->index('danhmucid');
            $table->unsignedInteger('ThuongHieuID')->nullable()->index('thuonghieuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanpham');
    }
};
