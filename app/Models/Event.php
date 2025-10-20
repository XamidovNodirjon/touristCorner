<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'title_uz',
        'title_en',
        'title_ru',
        'category_id',
        'description_uz',
        'description_en',
        'description_ru',
        'image',
        'event_date',
        'location',
        'time',
        'price',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }
}
