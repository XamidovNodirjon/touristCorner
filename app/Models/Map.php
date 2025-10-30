<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $table = 'maps';
    protected $fillable = [
        'name_uz', 'name_en', 'name_ru',
        'description_uz', 'description_en', 'description_ru',
        'latitude', 'longitude', 'image'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getNameAttribute()
    {
        $lang = session('locale', 'en');
        return $this->{'name_' . $lang} ?? $this->name_en;
    }

    public function getDescriptionAttribute()
    {
        $lang = session('locale', 'en');
        return $this->{'description_' . $lang} ?? $this->description_en;
    }
}
