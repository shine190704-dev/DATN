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
        Schema::table('sanpham', function (Blueprint $table) {
            $table->foreign(['NhaCungCapID'], 'sanpham_ibfk_1')->references(['NhaCungCapID'])->on('nhacungcap')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['DanhMucID'], 'sanpham_ibfk_2')->references(['DanhMucID'])->on('danhmuc')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['ThuongHieuID'], 'sanpham_ibfk_3')->references(['ThuongHieuID'])->on('thuonghieu')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            $table->dropForeign('sanpham_ibfk_1');
            $table->dropForeign('sanpham_ibfk_2');
            $table->dropForeign('sanpham_ibfk_3');
        });
    }
};
