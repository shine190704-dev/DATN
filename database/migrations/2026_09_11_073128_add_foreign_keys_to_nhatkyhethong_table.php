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
        Schema::table('nhatkyhethong', function (Blueprint $table) {
            $table->foreign(['NguoiThaoTacID'], 'nhatkyhethong_ibfk_1')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhatkyhethong', function (Blueprint $table) {
            $table->dropForeign('nhatkyhethong_ibfk_1');
        });
    }
};
