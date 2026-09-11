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
        Schema::table('lichsudonhang', function (Blueprint $table) {
            $table->foreign(['DonHangID'], 'lichsudonhang_ibfk_1')->references(['DonHangID'])->on('donhang')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['NguoiThayDoiID'], 'lichsudonhang_ibfk_2')->references(['NguoiDungID'])->on('nguoidung')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lichsudonhang', function (Blueprint $table) {
            $table->dropForeign('lichsudonhang_ibfk_1');
            $table->dropForeign('lichsudonhang_ibfk_2');
        });
    }
};
