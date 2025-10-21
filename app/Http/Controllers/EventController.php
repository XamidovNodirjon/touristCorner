<?php

namespace App\Http\Controllers;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
class EventController extends Controller
{
    public function index()
    {
        $categories = EventCategory::all();
        $events = Event::with('category')->latest()->get();

        return view('events.index', compact('events', 'categories'));
    }


    public function filterCategory($categoryId)
    {
        $categories = EventCategory::all();
        $category = EventCategory::find($categoryId);

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
