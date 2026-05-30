<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('show_peringkat')->default(true);
            $table->boolean('show_kehadiran')->default(true);
            $table->boolean('show_ekskul')->default(true);
            $table->boolean('show_catatan')->default(true);
            $table->boolean('show_deskripsi')->default(true);
            $table->boolean('show_kepribadian')->default(true);
            $table->string('template')->default('standard');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_settings');
    }
};
