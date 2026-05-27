<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained()->cascadeOnDelete();
            
            $table->integer('sakit')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('alpa')->default(0);

            $table->string('ekskul_1')->nullable();
            $table->string('nilai_ekskul_1')->nullable();
            $table->string('ekskul_2')->nullable();
            $table->string('nilai_ekskul_2')->nullable();
            $table->string('ekskul_3')->nullable();
            $table->string('nilai_ekskul_3')->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_wali_kelas');
    }
};
