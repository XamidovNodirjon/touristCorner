<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $table = 'maps';
    protected $fillable = [
        'name_uz',
        'name_en',
        'name_ru',
        'description_uz',
        'description_en',
        'description_ru',
        'latitude',
        'longitude',
        'image'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getTitleAttribute()
    {
        $lang = session('locale', 'en'); // default en
        return $this->{'title_' . $lang} ?? $this->title_en;
    }

    // 🔥 Dynamic description accessor
    public function getDescriptionAttribute()
    {
        $lang = session('locale', 'en');
        return $this->{'description_' . $lang} ?? $this->description_en;
    }
}
