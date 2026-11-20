<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoryController::class, 'index'])->name('home');
Route::get('/newest', [StoryController::class, 'newest'])->name('newest');
Route::get('/ask', [StoryController::class, 'ask'])->name('ask');
Route::get('/rss', [StoryController::class, 'rss'])->name('rss');
Route::get('/item/{story}', [StoryController::class, 'show'])->name('stories.show');

Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');

Route::get('/provinces', [ProvinceController::class, 'index'])->name('provinces.index');
Route::get('/province/{province}', [ProvinceController::class, 'show'])->name('provinces.show');

Route::get('/user/{user:username}', [UserController::class, 'show'])->name('users.show');
Route::get('/user/{user:username}/submissions', [UserController::class, 'submissions'])->name('users.submissions');
Route::get('/user/{user:username}/comments', [UserController::class, 'comments'])->name('users.comments');

Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/about', 'pages.about')->name('about');

Route::middleware('auth')->group(function () {
    Route::get('/submit', [StoryController::class, 'create'])->name('stories.create');
    Route::post('/submit', [StoryController::class, 'store'])->middleware('throttle:stories')->name('stories.store');

    Route::post('/item/{story}/comments', [CommentController::class, 'store'])->middleware('throttle:comments')->name('comments.store');

    Route::post('/vote', [VoteController::class, 'store'])->name('votes.store');
    Route::delete('/vote', [VoteController::class, 'destroy'])->name('votes.destroy');

    Route::patch('/user', [UserController::class, 'update'])->name('users.update');
});
