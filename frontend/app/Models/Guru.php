<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Guru extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['nama_guru', 'nip', 'jam_mingguan', 'mapel'];
    protected $table = 'guru';
    public $timestamps = false;
    protected $primaryKey = 'id_guru';
}
