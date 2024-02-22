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

Route::post('/user/{user}', \App\Http\Controllers\AuthorAction::class)->name('user.show');
Route::post('/home', \App\Http\Controllers\HomeAction::class)->name('home');
Route::post('/post/{post}', \App\Http\Controllers\ArticleAction::class)->name('post.show');
Route::post('/date/{date}', \App\Http\Controllers\ArticleDailyAction::class)->name('date.index');
Route::post('/attr/{attr}', \App\Http\Controllers\AttributeAction::class)->name('attr.index');

Route::get('/{any}', function(){return view(config('view.layout'));})->where('any', '.*');

