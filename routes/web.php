<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RunningScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

    // translate
    Route::get('/locale/{locale}', function (string $locale) {
        if (in_array($locale, ['en', 'id'])) {
            session()->put('locale', $locale);
        }

        return back();
    })->name('locale.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('running-schedules', RunningScheduleController::class);
    Route::get('/laju-ai', [ChatController::class, 'index'])->name('chat');
    Route::post('/laju-ai', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/laju-ai/clear', [ChatController::class, 'clear'])->name('chat.clear');
});

require __DIR__.'/auth.php';
