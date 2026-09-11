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
        Schema::table('yeucauhoantien', function (Blueprint $table) {
            $table->foreign(['DonHangID'], 'yeucauhoantien_ibfk_1')->references(['DonHangID'])->on('donhang')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['NguoiDungID'], 'yeucauhoantien_ibfk_2')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['NguoiXuLyID'], 'yeucauhoantien_ibfk_3')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('yeucauhoantien', function (Blueprint $table) {
            $table->dropForeign('yeucauhoantien_ibfk_1');
            $table->dropForeign('yeucauhoantien_ibfk_2');
            $table->dropForeign('yeucauhoantien_ibfk_3');
        });
    }
};
