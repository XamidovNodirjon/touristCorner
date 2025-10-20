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
}
