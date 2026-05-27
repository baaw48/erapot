<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mapel',
        'kelompok',
        'kktp',
        'urutan',
    ];

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}
