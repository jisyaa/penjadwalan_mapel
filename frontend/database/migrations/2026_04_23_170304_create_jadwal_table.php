<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id('id_jadwal');

            $table->unsignedBigInteger('id_master')->nullable();
            $table->unsignedBigInteger('id_guru_mapel');
            $table->unsignedBigInteger('id_waktu');

            // FOREIGN KEY
            $table->foreign('id_master')
                ->references('id_master')
                ->on('jadwal_master')
                ->onDelete('cascade'); // 🔥 penting biar ikut kehapus

            $table->foreign('id_guru_mapel')
                ->references('id_guru_mapel')
                ->on('guru_mapel')
                ->onDelete('cascade');

            $table->foreign('id_waktu')
                ->references('id_waktu')
                ->on('waktu')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal');
    }
};
