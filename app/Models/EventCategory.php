<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $table = 'event_categories';
    protected $fillable = ['name_uz', 'name_en', 'name_ru'];

    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    public function getNameAttribute()
    {
        $lang = session('locale', 'en');
        return $this->{'name_' . $lang} ?? $this->name_en;
    }

}
