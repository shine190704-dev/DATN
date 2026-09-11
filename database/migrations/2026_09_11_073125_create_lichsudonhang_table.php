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
        Schema::create('lichsudonhang', function (Blueprint $table) {
            $table->increments('LichSuDonHangID');
            $table->string('TrangThaiCu', 200)->nullable();
            $table->string('TrangThaiMoi', 200);
            $table->string('GhiChu', 100)->nullable();
            $table->dateTime('NgayCapNhat')->useCurrent();
            $table->unsignedInteger('DonHangID')->index('donhangid');
            $table->unsignedInteger('NguoiThayDoiID')->nullable()->index('nguoithaydoiid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lichsudonhang');
    }
};
