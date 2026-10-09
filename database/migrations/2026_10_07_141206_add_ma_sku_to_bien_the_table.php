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
        Schema::table('BienThe', function (Blueprint $table) {
            $table->string('MaSKU', 50)
                ->nullable()
                ->after('BienTheID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('BienThe', function (Blueprint $table) {
            $table->dropColumn('MaSKU');
        });
    }
};