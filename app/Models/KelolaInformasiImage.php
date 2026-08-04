<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelolaInformasiImage extends Model
{
    protected $table = 'kelola_informasi_images';

    protected $fillable = [
        'kelola_informasi_id',
        'image_path',
        'image_data',
    ];

    public function informasi()
    {
        return $this->belongsTo(KelolaInformasi::class, 'kelola_informasi_id');
    }
}
