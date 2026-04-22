<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Ruang extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['nama_ruang', 'tipe', 'kapasitas'];
    protected $table = 'ruang';
    public $timestamps = false;
    protected $primaryKey = 'id_ruang';
}
