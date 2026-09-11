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
        Schema::create('dondathang', function (Blueprint $table) {
            $table->increments('DonDatHangID');
            $table->string('MaDonDatHang', 50)->unique('madondathang');
            $table->string('TrangThai', 30)->default('DangXuLy');
            $table->decimal('TongTien', 14, 0);
            $table->decimal('SoTienDaThanhToan', 14, 0)->default(0);
            $table->decimal('SoTienConNo', 14, 0)->default(0);
            $table->dateTime('NgayDatHang');
            $table->dateTime('NgayDuKienNhan')->nullable();
            $table->dateTime('NgayNhanHang')->nullable();
            $table->text('GhiChu')->nullable();
            $table->dateTime('NgayTao')->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->unsignedInteger('NhaCungCapID')->index('nhacungcapid');
            $table->unsignedInteger('NguoiTaoID')->nullable()->index('nguoitaoid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dondathang');
    }
};
