<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Map;
use Illuminate\Http\Request;

class MainController extends Controller
{

    public function welcome()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        $librariesCount = \App\Models\Library::count();
        $eventsCount = \App\Models\Event::count();
        $mapsCount = \App\Models\Map::count();
        return view('admin.dashboard', compact('librariesCount', 'eventsCount', 'mapsCount'));
    }

    public function map()
    {
        $locale = session('locale', 'en');

        $maps = Map::all()->map(function ($map) use ($locale) {
            return [
                'id' => $map->id,
                'name' => $map->{'name_' . $locale} ?? $map->name_uz,
                'description' => $map->{'description_' . $locale} ?? $map->description_uz,
                'latitude' => $map->latitude,
                'longitude' => $map->longitude,
                'image_url' => $map->image_url,
            ];
        });

        return view('map.index', compact('maps'));
    }
}
