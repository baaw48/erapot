<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->string('alamat')->nullable();
            $table->string('kepala_sekolah')->nullable();
            $table->string('nip_kepsek')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('npsn')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
