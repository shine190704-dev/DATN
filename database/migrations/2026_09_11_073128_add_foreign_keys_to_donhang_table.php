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
        Schema::table('donhang', function (Blueprint $table) {
            $table->foreign(['NguoiDungID'], 'donhang_ibfk_1')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['MaGiamGiaID'], 'donhang_ibfk_2')->references(['MaGiamGiaID'])->on('magiamgia')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->dropForeign('donhang_ibfk_1');
            $table->dropForeign('donhang_ibfk_2');
        });
    }
};
