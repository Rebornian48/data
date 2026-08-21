<?php

use App\Http\Controllers\Admin\CaptainController;
use App\Http\Controllers\Admin\GenerationController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\SingleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ---------- Public / Dashboard ----------
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/members', [DashboardController::class, 'members'])->name('members.index');
Route::get('/members/{member}', [DashboardController::class, 'member'])->name('members.show');

// ---------- Auth ----------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ---------- Admin ----------
Route::prefix('admin')->name('admin.')->middleware(['admin.auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.members.index');
    })->name('home');

    Route::resource('members', AdminMemberController::class);
    Route::resource('singles', SingleController::class)->except(['show']);
    Route::resource('generations', GenerationController::class)->except(['show']);
    Route::resource('captains', CaptainController::class)->except(['show']);
});
