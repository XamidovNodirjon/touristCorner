<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryCategory extends Model
{
    protected $table = 'library_categories';
    protected $fillable = ['name'];




    public function libraries()
    {
        return $this->hasMany(Library::class, 'category_id');
    }
}
