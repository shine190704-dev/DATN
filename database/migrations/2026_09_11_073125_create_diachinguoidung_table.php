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
        Schema::create('diachinguoidung', function (Blueprint $table) {
            $table->increments('DiaChiNguoiDungID');
            $table->string('TenNguoiNhan', 200);
            $table->string('SoDienThoai', 10);
            $table->string('DiaChi', 225);
            $table->boolean('MacDinh')->default(false);
            $table->dateTime('NgayTao')->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->useCurrent();
            $table->unsignedInteger('NguoiDungID')->index('nguoidungid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diachinguoidung');
    }
};
