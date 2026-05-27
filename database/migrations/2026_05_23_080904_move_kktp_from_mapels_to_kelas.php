<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Remove kktp from mapels
        Schema::table('mapels', function (Blueprint $table) {
            $table->dropColumn('kktp');
        });

        // 2. Add kktp to kelas
        Schema::table('kelas', function (Blueprint $table) {
            $table->integer('kktp')->default(75)->after('wali_kelas_id');
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('kktp');
        });

        Schema::table('mapels', function (Blueprint $table) {
            $table->integer('kktp')->default(75);
        });
    }
};
