<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmMenuImage extends Model
{
    protected $table = 'umkm_menu_images';

    protected $fillable = [
        'umkm_id',
        'image_path',
        'image_data',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
