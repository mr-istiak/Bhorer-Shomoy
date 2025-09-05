<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Epaper\EpaperController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PostController;
use App\Models\User;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TranslationController;
use App\Http\Middleware\VerifyApiRoute;
use App\Models\Epaper;
use App\Models\Post;

Route::redirect('/', '/dashboard')->name('home');
// Posts (admin)
Route::middleware(['auth', 'verified'])->group(function () {
	Route::get('dashboard', function () {
		return Inertia::render('Dashboard');
	})->name('dashboard');

	Route::resource('posts', PostController::class)->except(['show']);
    Route::post('/posts/{post}/status', [PostController::class, 'updateStatus'])->name('posts.change_status')->can('actionStatus', Post::class);

	Route::get('/users', [RegisteredUserController::class, 'index'])->name('users.index')->can('viewAny', User::class);
	Route::delete('/user/{user}', [RegisteredUserController::class, 'destroy'])->name('users.destroy')->can('destroy', 'user');

	Route::resource('media', MediaController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy', 'update']);
	Route::resource('position', PositionController::class)->only(['index', 'update', 'destroy']);
	Route::resource('epapers', EpaperController::class)->only(['index', 'store', 'edit']);

	Route::post('/translate/from/{fromLang}/to/{toLang}', [TranslationController::class, 'translate'])->name('translate');
	Route::get('/epages/{epage}', [EpaperController::class, 'showPage'])->name('epages.show')->can('update', Epaper::class);
	Route::post('/epages/{epage}/articles', [EpaperController::class, 'storeArticles'])->name('epage.articles.store')->can('update', Epaper::class);
	Route::post('/epaper/{epaper}/generate', [EpaperController::class, 'generate'])->name('epaper.generate')->can('generate', Epaper::class);
});
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/api.php';

