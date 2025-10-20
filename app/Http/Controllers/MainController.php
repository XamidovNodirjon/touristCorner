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
        $maps = Map::all();
        return view('map.index', compact('maps'));
    }
}
