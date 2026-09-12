<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('nguoidung', 'NgaySinh')) {
            Schema::table('nguoidung', function (Blueprint $table) {
                $table->date('NgaySinh')->nullable()->after('SoDienThoai');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('nguoidung', 'NgaySinh')) {
            Schema::table('nguoidung', function (Blueprint $table) {
                $table->dropColumn('NgaySinh');
            });
        }
    }
};
