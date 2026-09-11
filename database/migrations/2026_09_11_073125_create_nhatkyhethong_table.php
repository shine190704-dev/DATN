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
        Schema::create('nhatkyhethong', function (Blueprint $table) {
            $table->increments('NhatKyHeThongID');
            $table->string('PhanHe', 100);
            $table->string('HanhDong', 100);
            $table->string('DoiTuong', 100);
            $table->text('MoTa')->nullable();
            $table->dateTime('NgayTao')->useCurrent();
            $table->unsignedInteger('NguoiThaoTacID')->index('nguoithaotacid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhatkyhethong');
    }
};
