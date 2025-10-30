<?php

namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Library;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;
class EventController extends Controller
{
    public function index()
    {
        $categories = LibraryCategory::all();
        $events = Event::with('category')->latest()->get();

        return view('events.index', compact('events', 'categories'));
    }


    public function filterCategory($categoryId)
    {
        $categories = LibraryCategory::all();
        $category = LibraryCategory::find($categoryId);

        if (!$category) {
            return view('events.index', [
                'events' => collect(),
                'categories' => $categories
            ]);
        }

        $events = Event::where('category_id', $categoryId)->get();

        return view('events.index', compact('events', 'categories'));
    }
}
