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

Route::get('/user/{user}/show', \App\Http\Controllers\AuthorAction::class)->name('user.show');
Route::get('/post/{post}/show', \App\Http\Controllers\ArticleAction::class)->name('post.show');
Route::get('/date/{date}', \App\Http\Controllers\ArticleDailyAction::class)->name('date.index');
Route::get('/attr/{attr}', \App\Http\Controllers\AttributeAction::class)->name('attr.index');