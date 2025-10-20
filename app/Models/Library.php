<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $fillable = [
        'title_uz',
        'title_en',
        'title_ru',
        'description_uz',
        'description_en',
        'description_ru',
        'category_id',
        'image',
        'file_path_ru',
        'file_path_en',
    ];

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }
}
