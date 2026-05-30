<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaporSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_peringkat',
        'show_kehadiran',
        'show_ekskul',
        'show_catatan',
        'show_deskripsi',
        'show_kepribadian',
        'template',
    ];

    protected $casts = [
        'show_peringkat' => 'boolean',
        'show_kehadiran' => 'boolean',
        'show_ekskul' => 'boolean',
        'show_catatan' => 'boolean',
        'show_deskripsi' => 'boolean',
        'show_kepribadian' => 'boolean',
    ];
}