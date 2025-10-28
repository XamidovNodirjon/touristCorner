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
        'file_path_uz',
    ];

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'category_id');
    }

    public function getTitleAttribute()
    {
        $lang = session('locale', 'uz'); // default uz
        return $this->{'title_' . $lang} ?? $this->title_uz;
    }

    // 🔥 Dynamic description accessor
    public function getDescriptionAttribute()
    {
        $lang = session('locale', 'uz');
        return $this->{'description_' . $lang} ?? $this->description_uz;
    }
}
