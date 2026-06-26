<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaKalender extends Model
{
    protected $table = 'agenda_kalender';

    protected $fillable = [
        'title',
        'event_date',
        'start_time',
        'end_time',
        'description',
    ];
}
