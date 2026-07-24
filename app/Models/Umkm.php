<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Umkm extends Model
{
    protected $table = 'umkm';

    protected $fillable = [
        'name',
        'photo_path',
        'photo_data',
        'menu_path',
        'menu_data',
        'description',
        'contact_name',
        'whatsapp_link',
    ];

    public function menuImages(): HasMany
    {
        return $this->hasMany(UmkmMenuImage::class);
    }
}
