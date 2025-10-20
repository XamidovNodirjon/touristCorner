<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventController as ControllersEventController;
use App\Http\Controllers\LibraryController as ControllersLibraryController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;
use PharIo\Manifest\Email;

Route::get('/', [MainController::class, 'welcome'])->name('welcome');

Route::get('uzbekistan-map', [MainController::class, 'map'])->name('map-road');
Route::get('admin/get-login', [AuthController::class, 'getLogin']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

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







Route::middleware('auth')->group(function () {

   Route::get('dashboard',[MainController::class, 'dashboard'])->name('admin.dashboard');
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

});