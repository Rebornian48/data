<?php

use App\Http\Controllers\Admin\CaptainController;
use App\Http\Controllers\Admin\GenerationController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\SingleController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SorterController;
use App\Http\Controllers\Webhooks\DiscordWebhookController;
use App\Http\Controllers\Webhooks\TelegramWebhookController;
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
Route::get('/singles', [DashboardController::class, 'singles'])->name('singles.index');
Route::get('/captains', [DashboardController::class, 'captains'])->name('captains.index');
Route::get('/statistik', [DashboardController::class, 'statistik'])->name('statistik.index');
Route::get('/restrukturisasi', [DashboardController::class, 'restrukturisasi'])->name('restrukturisasi.index');
Route::get('/kalender', [DashboardController::class, 'calendar'])->name('calendar.index');

// ---------- Peta (public map) ----------
Route::get('/peta',            [PetaController::class, 'show'])->name('peta.default');
Route::get('/peta/{slug}',     [PetaController::class, 'show'])->name('peta.show');
Route::get('/api/peta/{slug}', [PetaController::class, 'data'])->name('peta.data');

// ---------- Sorter ----------
Route::get('/sorter', [SorterController::class, 'index'])->name('sorter.index');
Route::get('/sorter/{type}', [SorterController::class, 'show'])->name('sorter.show');

// ---------- Bot webhooks ----------
Route::post('/webhooks/telegram/{secret}', TelegramWebhookController::class)->name('webhooks.telegram');
Route::post('/webhooks/discord', DiscordWebhookController::class)->name('webhooks.discord');

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
    Route::resource('teams', TeamController::class)->except(['show']);
    Route::resource('maps', MapController::class)->except(['show']);

    Route::get('/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
});
