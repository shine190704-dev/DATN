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
        Schema::table('danhsachyeuthich', function (Blueprint $table) {
            $table->foreign(['NguoiDungID'], 'danhsachyeuthich_ibfk_1')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['SanPhamID'], 'danhsachyeuthich_ibfk_2')->references(['SanPhamID'])->on('sanpham')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('danhsachyeuthich', function (Blueprint $table) {
            $table->dropForeign('danhsachyeuthich_ibfk_1');
            $table->dropForeign('danhsachyeuthich_ibfk_2');
        });
    }
};
