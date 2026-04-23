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
        Schema::create('jadwal_master', function (Blueprint $table) {
            $table->id('id_master');
            $table->string('tahun_ajaran', 20)->nullable();
            $table->enum('semester', ['ganjil', 'genap'])->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('tanggal_generate')->useCurrent();
            $table->enum('aktif', ['aktif', 'tidak'])->default('tidak');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_master');
    }
};
