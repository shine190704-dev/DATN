
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
        Schema::table('MaGiamGia', function (Blueprint $table) {
            $table->string('LoaiGiamGia', 20)
                ->default('PhanTram');

            $table->unsignedInteger('SoLuongPhatHanh')
                ->default(0);

            $table->unsignedInteger('SoLuongDaCap')
                ->default(0);

            $table->dateTime('NgayBatDau')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('MaGiamGia', function (Blueprint $table) {
            $table->dropColumn([
                'LoaiGiamGia',
                'SoLuongPhatHanh',
                'SoLuongDaCap',
                'NgayBatDau',
            ]);
        });
    }
};
