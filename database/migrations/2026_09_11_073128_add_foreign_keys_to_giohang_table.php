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
        Schema::table('giohang', function (Blueprint $table) {
            $table->foreign(['NguoiDungID'], 'giohang_ibfk_1')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('giohang', function (Blueprint $table) {
            $table->dropForeign('giohang_ibfk_1');
        });
    }
};
