<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Waktu extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['hari', 'jam_ke', 'waktu_mulai', 'waktu_selesai', 'keterangan'];
    protected $table = 'waktu';
    public $timestamps = false;
    protected $primaryKey = 'id_waktu';

    // Accessor untuk menampilkan label jam
    public function getJamLabelAttribute()
    {
        if (is_null($this->jam_ke)) {
            return 'Khusus';
        }
        return $this->jam_ke;
    }

    // Scope untuk mengambil waktu yang memiliki jam_ke
    public function scopeWithJam($query)
    {
        return $query->whereNotNull('jam_ke');
    }

    // Scope untuk mengambil waktu tanpa jam_ke (kegiatan khusus)
    public function scopeWithoutJam($query)
    {
        return $query->whereNull('jam_ke');
    }
}
