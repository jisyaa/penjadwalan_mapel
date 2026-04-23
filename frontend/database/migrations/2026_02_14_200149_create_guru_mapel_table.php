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
        Schema::create('guru_mapel', function (Blueprint $table) {
            $table->id('id_guru_mapel');

            $table->unsignedBigInteger('id_guru');
            $table->unsignedBigInteger('id_mapel');
            $table->unsignedBigInteger('id_kelas');

            $table->enum('aktif', ['aktif', 'tidak'])->default('aktif');

            // FOREIGN KEY
            $table->foreign('id_guru')
                ->references('id_guru')
                ->on('guru')
                ->cascadeOnDelete();

            $table->foreign('id_mapel')
                ->references('id_mapel')
                ->on('mapel')
                ->cascadeOnDelete();

            $table->foreign('id_kelas')
                ->references('id_kelas')
                ->on('kelas')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_mapel');
    }
};
