<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class GuruMapel extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['id_guru', 'id_mapel', 'id_kelas', 'aktif'];
    protected $table = 'guru_mapel';
    public $timestamps = false;
    protected $primaryKey = 'id';

    public function r_guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function r_mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function r_kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }
}
