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
        'start_date',
        'end_date',
        'location',
        'time',
        'price',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }

    public function getTitleAttribute()
    {
        $lang = session('locale', 'en'); // default uz
        return $this->{'title_' . $lang} ?? $this->title_en;
    }

    // 🔥 Dynamic description accessor
    public function getDescriptionAttribute()
    {
        $lang = session('locale', 'en');
        return $this->{'description_' . $lang} ?? $this->description_en;
    }
}
