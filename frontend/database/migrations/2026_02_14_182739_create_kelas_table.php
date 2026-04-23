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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id('id_kelas');
            $table->string('nama_kelas');
            $table->integer('tingkat');
            $table->integer('jumlah_siswa');

            $table->unsignedBigInteger('wali_kelas')->nullable(); // ✅ FIX
            $table->unsignedBigInteger('id_ruang');

            $table->foreign('wali_kelas')
                ->references('id_guru')
                ->on('guru')
                ->nullOnDelete();

            $table->foreign('id_ruang')
                ->references('id_ruang')
                ->on('ruang')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
