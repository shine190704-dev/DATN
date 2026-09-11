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
        Schema::table('chitietgiohang', function (Blueprint $table) {
            $table->foreign(['GioHangID'], 'chitietgiohang_ibfk_1')->references(['GioHangID'])->on('giohang')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['BienTheID'], 'chitietgiohang_ibfk_2')->references(['BienTheID'])->on('bienthe')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chitietgiohang', function (Blueprint $table) {
            $table->dropForeign('chitietgiohang_ibfk_1');
            $table->dropForeign('chitietgiohang_ibfk_2');
        });
    }
};
