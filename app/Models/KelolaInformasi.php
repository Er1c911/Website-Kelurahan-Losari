<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelolaInformasi extends Model
{
    protected $table = 'kelola_informasi';

    protected $fillable = [
        'key',
        'title',
        'description',
        'image_path',
        'image_data',
    ];
}

