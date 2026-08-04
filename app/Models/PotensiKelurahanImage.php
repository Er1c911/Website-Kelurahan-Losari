<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PotensiKelurahanImage extends Model
{
    protected $table = 'potensi_kelurahan_images';

    protected $fillable = [
        'potensi_kelurahan_item_id',
        'image_path',
        'image_data',
    ];

    public function potensi(): BelongsTo
    {
        return $this->belongsTo(PotensiKelurahanItem::class, 'potensi_kelurahan_item_id');
    }
}
