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
        Schema::table('capmagiamgia', function (Blueprint $table) {
            $table->foreign(['MaGiamGiaID'], 'capmagiamgia_ibfk_1')->references(['MaGiamGiaID'])->on('magiamgia')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['NguoiDungID'], 'capmagiamgia_ibfk_2')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capmagiamgia', function (Blueprint $table) {
            $table->dropForeign('capmagiamgia_ibfk_1');
            $table->dropForeign('capmagiamgia_ibfk_2');
        });
    }
};
