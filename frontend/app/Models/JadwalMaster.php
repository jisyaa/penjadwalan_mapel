<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMaster extends Model
{
    protected $table = 'jadwal_master';
    protected $primaryKey = 'id_master';
    public $timestamps = false;

    protected $fillable = [
        'tahun_ajaran', 'semester', 'keterangan', 'tanggal_generate', 'aktif'
    ];

    protected $casts = [
        'tanggal_generate' => 'datetime'
    ];

    public function details()
    {
        return $this->hasMany(Jadwal::class, 'id_master', 'id_master');
    }
}
