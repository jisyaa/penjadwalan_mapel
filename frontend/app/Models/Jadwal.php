<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;

    protected $fillable = [
        'id_master', 'id_guru_mapel', 'id_waktu'
    ];

    public function master()
    {
        return $this->belongsTo(JadwalMaster::class, 'id_master', 'id_master');
    }

    public function guruMapel()
    {
        return $this->belongsTo(GuruMapel::class, 'id_guru_mapel', 'id_guru_mapel');
    }

    public function waktu()
    {
        return $this->belongsTo(Waktu::class, 'id_waktu', 'id_waktu');
    }
}
