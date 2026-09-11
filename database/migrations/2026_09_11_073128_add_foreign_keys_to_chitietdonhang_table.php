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
        Schema::table('chitietdonhang', function (Blueprint $table) {
            $table->foreign(['DonHangID'], 'chitietdonhang_ibfk_1')->references(['DonHangID'])->on('donhang')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['SanPhamID'], 'chitietdonhang_ibfk_2')->references(['SanPhamID'])->on('sanpham')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chitietdonhang', function (Blueprint $table) {
            $table->dropForeign('chitietdonhang_ibfk_1');
            $table->dropForeign('chitietdonhang_ibfk_2');
        });
    }
};
