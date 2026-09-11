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
        Schema::table('danhgia', function (Blueprint $table) {
            $table->foreign(['NguoiDungID'], 'danhgia_ibfk_1')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['SanPhamID'], 'danhgia_ibfk_2')->references(['SanPhamID'])->on('sanpham')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['DonHangID'], 'danhgia_ibfk_3')->references(['DonHangID'])->on('donhang')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('danhgia', function (Blueprint $table) {
            $table->dropForeign('danhgia_ibfk_1');
            $table->dropForeign('danhgia_ibfk_2');
            $table->dropForeign('danhgia_ibfk_3');
        });
    }
};
