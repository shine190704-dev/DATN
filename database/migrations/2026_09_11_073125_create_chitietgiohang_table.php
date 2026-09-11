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
        Schema::create('chitietgiohang', function (Blueprint $table) {
            $table->increments('ChiTietGioHangID');
            $table->unsignedInteger('SoLuong')->default(1);
            $table->unsignedInteger('GioHangID');
            $table->unsignedInteger('BienTheID')->index('bientheid');

            $table->unique(['GioHangID', 'BienTheID'], 'uq_gio_bienthe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitietgiohang');
    }
};
