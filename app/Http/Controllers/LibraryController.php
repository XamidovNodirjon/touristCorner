<?php

namespace App\Http\Controllers;

use App\Mail\MaterialMail;
use App\Models\Library;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LibraryController extends Controller
{
    public function index()
    {
        $materials = Library::all();
        $categories = LibraryCategory::all();
        return view('library.index', compact('materials', 'categories'));
    }


    public function filterCategory($categoryId)
    {
        $categories = LibraryCategory::all();
        $category = LibraryCategory::find($categoryId);

        // Agar kategoriya topilmasa — bo‘sh sahifa
        if (!$category) {
            return view('library.index', [
                'materials' => collect(),
                'categories' => $categories
            ]);
        }

        $materials = Library::where('category_id', $categoryId)->get();

        return view('library.index', compact('materials', 'categories'));
    }



}
