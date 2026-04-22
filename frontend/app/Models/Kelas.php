<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Kelas extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['nama_kelas', 'tingkat', 'jumlah_siswa', 'wali_kelas', 'id_ruang'];
    protected $table = 'kelas';
    public $timestamps = false;
    protected $primaryKey = 'id_kelas';

    public function r_guru()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas', 'id_guru');
    }

    public function r_ruang()
    {
        return $this->belongsTo(Ruang::class, 'id_ruang', 'id_ruang');
    }
}
