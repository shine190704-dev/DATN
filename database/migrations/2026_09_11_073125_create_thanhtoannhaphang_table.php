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
        Schema::create('thanhtoannhaphang', function (Blueprint $table) {
            $table->increments('ThanhToanNhapHangID');
            $table->decimal('SoTienThanhToan', 14, 0);
            $table->string('PhuongThucThanhToan', 50);
            $table->dateTime('NgayThanhToan')->useCurrent();
            $table->text('GhiChu')->nullable();
            $table->unsignedInteger('DonDatHangID')->index('dondathangid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanhtoannhaphang');
    }
};
