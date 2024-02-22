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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user/{user}/show', \App\Http\Controllers\AuthorAction::class)->name('user.show');
Route::get('/user', \App\Http\Controllers\AuthorListAction::class)->name('user.index');
Route::get('/post/{post}/show', \App\Http\Controllers\ArticleAction::class)->name('post.show');
Route::get('/post', \App\Http\Controllers\ArticleListAction::class)->name('post.index');
Route::get('/date/{date}', \App\Http\Controllers\ArticleDailyAction::class)->name('date.index');
