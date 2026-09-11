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
        Schema::table('thanhtoannhaphang', function (Blueprint $table) {
            $table->foreign(['DonDatHangID'], 'thanhtoannhaphang_ibfk_1')->references(['DonDatHangID'])->on('dondathang')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('thanhtoannhaphang', function (Blueprint $table) {
            $table->dropForeign('thanhtoannhaphang_ibfk_1');
        });
    }
};
