<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventController as ControllersEventController;
use App\Http\Controllers\LibraryController as ControllersLibraryController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\EmailController;
use \App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use PharIo\Manifest\Email;


Route::middleware(['web'])->group(function () {

    // 🌍 Til almashtirish (har kim uchun ochiq)
    Route::get('lang/{locale}', function ($locale) {
        if (in_array($locale, ['uz', 'en', 'ru'])) {
            Session::put('locale', $locale);
        }
        return redirect()->back();
    })->name('lang.switch');

});

Route::get('/', [MainController::class, 'welcome'])->name('welcome');

Route::get('admin/get-login', [AuthController::class, 'getLogin'])->name('login.form');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('uzbekistan-map', [MainController::class, 'map'])->name('map-road');

//library
Route::get('get-libraries', [ControllersLibraryController::class, 'index'])->name('libraries.index');
Route::get('/libraries', [ControllersLibraryController::class, 'index'])->name('libraries.index');
Route::get('/libraries/category/{id}', [ControllersLibraryController::class, 'filterCategory'])->name('libraries.filter');


//events
Route::get('get-events', [ControllersEventController::class, 'index'])->name('events.index');

Route::get('/events', [ControllersEventController::class, 'index'])->name('events.index');
Route::get('/events/category/{id}', [ControllersEventController::class, 'filterCategory'])->name('events.filter');


Route::post('/send-material', [EmailController::class, 'sendMaterial'])->name('send.material');
Route::post('/send-event', [EmailController::class, 'sendEvent'])->name('send.event');
Route::post('/send-map-email', [EmailController::class, 'sendMap'])->name('send.map.email');


Route::middleware('auth')->group(function () {

    Route::get('dashboard', [MainController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('maps', [MapController::class, 'index'])->name('admin.maps');
    Route::get('maps/create', [MapController::class, 'create'])->name('admin.maps.create');
    Route::post('maps', [MapController::class, 'store'])->name('admin.maps.store');
    Route::get('maps/{id}/edit', [MapController::class, 'edit'])->name('admin.maps.edit');
    Route::put('maps/{id}', [MapController::class, 'update'])->name('admin.maps.update');
    Route::delete('maps/{id}', [MapController::class, 'destroy'])->name('admin.maps.destroy');

    //library
    Route::get('libraries', [LibraryController::class, 'index'])->name('admin.libraries');
    Route::get('libraries/create', [LibraryController::class, 'create'])->name('admin.libraries.create');
    Route::post('libraries', [LibraryController::class, 'store'])->name('admin.libraries.store');
    Route::get('libraries/{id}/edit', [LibraryController::class, 'edit'])->name('admin.libraries.edit');
    Route::put('libraries/{id}', [LibraryController::class, 'update'])->name('admin.libraries.update');
    Route::delete('libraries/{id}', [LibraryController::class, 'destroy'])->name('admin.libraries.destroy');

    //events
    Route::get('events', [EventController::class, 'index'])->name('admin.events');
    Route::post('events', [EventController::class, 'store'])->name('admin.events.store');
    Route::put('events/{id}', [EventController::class, 'update'])->name('admin.events.update');
    Route::delete('events/{id}', [EventController::class, 'destroy'])->name('admin.events.destroy');

    //userInfo
    Route::get('user-info', [UserController::class, 'index'])->name('admin.security.index');
    Route::put('user-info-update/{id}', [UserController::class, 'update'])->name('admin.security.update');


});