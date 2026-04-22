<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Mapel extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['nama_mapel', 'jam_per_minggu', 'kategori'];
    protected $table = 'mapel';
    public $timestamps = false;
    protected $primaryKey = 'id_mapel';
}
