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
        Schema::table('dondathang', function (Blueprint $table) {
            $table->foreign(['NhaCungCapID'], 'dondathang_ibfk_1')->references(['NhaCungCapID'])->on('nhacungcap')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['NguoiTaoID'], 'dondathang_ibfk_2')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dondathang', function (Blueprint $table) {
            $table->dropForeign('dondathang_ibfk_1');
            $table->dropForeign('dondathang_ibfk_2');
        });
    }
};
