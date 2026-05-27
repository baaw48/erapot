<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('password');
            $table->string('mapel_diampu')->nullable()->after('nip');
            $table->string('kelas_diampu')->nullable()->after('mapel_diampu');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'mapel_diampu', 'kelas_diampu']);
        });
    }
};
