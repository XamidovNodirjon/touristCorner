<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryCategory extends Model
{
    protected $table = 'library_categories';
    protected $fillable = ['name_uz', 'name_en', 'name_ru'];


    public function libraries()
    {
        return $this->hasMany(Library::class, 'category_id');
    }

    public function getNameAttribute()
    {
        $lang = session('locale', 'en');
        return $this->{'name_' . $lang} ?? $this->name_en;
    }
}
