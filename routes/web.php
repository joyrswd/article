<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
if ($lang = filter_input(INPUT_SERVER, 'HTTP_LANG')) {
    app()->setLocale($lang);
}

Route::post('/home', \App\Http\Controllers\HomeAction::class)->name('home');
Route::post('/user/{user}', \App\Http\Controllers\AuthorAction::class)->name('user.show');
Route::post('/post/{post}', \App\Http\Controllers\ArticleAction::class)->name('post.show');
Route::post('/date/{date}', \App\Http\Controllers\ArticleDailyAction::class)->name('date.index');
Route::post('/attr/{attr}', \App\Http\Controllers\AttributeAction::class)->name('attr.index');
Route::post('/gallery', \App\Http\Controllers\GalleryAction::class)->name('gallery.index');
Route::post('/contact', \App\Http\Controllers\ContactAction::class)->middleware('throttle:2,1')->name('contact');

Route::get('/{any}', function(){return view(config('view.layout'));})->where('any', '.*');

