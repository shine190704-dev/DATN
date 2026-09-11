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
        Schema::table('chitietdondathang', function (Blueprint $table) {
            $table->foreign(['DonDatHangID'], 'chitietdondathang_ibfk_1')->references(['DonDatHangID'])->on('dondathang')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['SanPhamID'], 'chitietdondathang_ibfk_2')->references(['SanPhamID'])->on('sanpham')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chitietdondathang', function (Blueprint $table) {
            $table->dropForeign('chitietdondathang_ibfk_1');
            $table->dropForeign('chitietdondathang_ibfk_2');
        });
    }
};
