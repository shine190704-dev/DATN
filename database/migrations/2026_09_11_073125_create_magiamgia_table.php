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
        Schema::create('magiamgia', function (Blueprint $table) {
            $table->increments('MaGiamGiaID');
            $table->string('MaCode', 50)->unique('macode');
            $table->decimal('GiaTriGiam', 12, 0);
            $table->decimal('GiaTriDonHangToiThieu', 12, 0)->default(0);
            $table->dateTime('NgayHetHan');
            $table->string('TrangThai', 30)->default('HoatDong');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magiamgia');
    }
};
