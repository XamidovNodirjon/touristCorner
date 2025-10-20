<?php

namespace App\Http\Controllers\Admin;

use App\Models\Map;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapController extends Controller
{

    

    public function index()
    {
        $maps = \App\Models\Map::all();
        return view('admin.maps.index', compact('maps'));
    }

    public function create()
    {
        return view('admin.maps.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_uz' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|max:5048',
        ]);

        $data = $request->only(['latitude', 'longitude', 'name_uz', 'name_ru', 'name_en', 'description_uz', 'description_ru', 'description_en']);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('maps', 'public');
            $data['image'] = $imagePath;
        }

        \App\Models\Map::create($data);

        return redirect()->route('admin.maps')->with('success', 'Map data created successfully.');
    }
    
    public function destroy($id)
    {
        $map = \App\Models\Map::findOrFail($id);
        if ($map->image) {
            \Storage::disk('public')->delete($map->image);
        }
        $map->delete();

        return redirect()->route('admin.maps')->with('success', 'Map data deleted successfully.');
    }
}
