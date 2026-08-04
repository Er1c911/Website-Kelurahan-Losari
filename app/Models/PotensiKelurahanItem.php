<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PotensiKelurahanItem extends Model
{
    protected $table = 'potensi_kelurahan_items';

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'image_data',
        'sort_order',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PotensiKelurahanImage::class, 'potensi_kelurahan_item_id');
    }
}
