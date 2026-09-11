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
        Schema::table('diachinguoidung', function (Blueprint $table) {
            $table->foreign(['NguoiDungID'], 'diachinguoidung_ibfk_1')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diachinguoidung', function (Blueprint $table) {
            $table->dropForeign('diachinguoidung_ibfk_1');
        });
    }
};
