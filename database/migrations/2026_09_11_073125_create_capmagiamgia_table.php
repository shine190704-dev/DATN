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
        Schema::create('capmagiamgia', function (Blueprint $table) {
            $table->increments('CapMaGiamGiaID');
            $table->boolean('DaSuDung')->default(false);
            $table->dateTime('NgayCap')->useCurrent();
            $table->dateTime('NgaySuDung')->nullable();
            $table->unsignedInteger('MaGiamGiaID');
            $table->unsignedInteger('NguoiDungID')->index('nguoidungid');

            $table->unique(['MaGiamGiaID', 'NguoiDungID'], 'uq_cap_ma');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capmagiamgia');
    }
};
