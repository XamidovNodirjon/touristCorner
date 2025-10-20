<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::latest()->get();
        $categories = EventCategory::all();
        return view('admin.events.index', compact('events', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'description_uz' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'time' => 'nullable',
            'price' => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->back()->with('success', 'Tadbir muvaffaqiyatli qo‘shildi!');
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title_uz' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'title_ru' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'description_uz' => 'nullable|string',
            'description_en' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'event_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'time' => 'nullable',
            'price' => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->back()->with('success', 'Tadbir yangilandi!');
    }

    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->back()->with('success', 'Tadbir o‘chirildi!');
    }
}
