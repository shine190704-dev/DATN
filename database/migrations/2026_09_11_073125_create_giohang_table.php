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
        Schema::create('giohang', function (Blueprint $table) {
            $table->increments('GioHangID');
            $table->dateTime('NgayTao')->useCurrent();
            $table->dateTime('NgayCapNhat')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->unsignedInteger('NguoiDungID')->unique('nguoidungid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giohang');
    }
};
