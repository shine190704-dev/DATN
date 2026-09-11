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
        Schema::create('yeucauhoantien', function (Blueprint $table) {
            $table->increments('YeuCauHoanTienID');
            $table->string('LyDo', 100);
            $table->text('MoTa');
            $table->string('AnhMinhChung', 225)->nullable();
            $table->string('VideoMinhChung', 225)->nullable();
            $table->decimal('SoTienHoan', 12, 0)->nullable();
            $table->string('TrangThai', 30)->default('DangXuLy');
            $table->string('GhiChuXuLy', 225)->nullable();
            $table->dateTime('NgayYeuCau')->useCurrent();
            $table->dateTime('NgayXuLy')->nullable();
            $table->unsignedInteger('DonHangID')->index('donhangid');
            $table->unsignedInteger('NguoiDungID')->index('nguoidungid');
            $table->unsignedInteger('NguoiXuLyID')->nullable()->index('nguoixulyid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeucauhoantien');
    }
};
