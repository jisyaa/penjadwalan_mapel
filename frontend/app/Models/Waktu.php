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
}
