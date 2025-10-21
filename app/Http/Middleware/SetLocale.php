<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        // Sessiyadan tilni olish, agar yo‘q bo‘lsa — inglizcha default
        $locale = Session::get('locale', 'en');

        // Laravel tilini o‘rnatish
        App::setLocale($locale);

        return $next($request);
    }
}
