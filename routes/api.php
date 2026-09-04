<?php

use App\Http\Controllers\Api\AlbumController;
use App\Http\Controllers\Api\CaptainController;
use App\Http\Controllers\Api\CouplingSongController;
use App\Http\Controllers\Api\DocsController;
use App\Http\Controllers\Api\GenerationController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\MvLocationController;
use App\Http\Controllers\Api\SetlistController;
use App\Http\Controllers\Api\SingleController;
use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\SubUnitController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Prefix "/api" is applied at registration in bootstrap/app.php.
| All responses are JSON.
*/

// ---------- Swagger UI ----------
Route::get('/docs', [DocsController::class, 'ui'])->name('api.docs');
Route::get('/docs/openapi.json', [DocsController::class, 'spec'])->name('api.docs.spec');

// ---------- v1 ----------
Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [MemberController::class, 'show'])
        ->whereNumber('member')
        ->name('members.show');

    Route::get('/singles', [SingleController::class, 'index'])->name('singles.index');
    Route::get('/singles/{single}', [SingleController::class, 'show'])
        ->whereNumber('single')
        ->name('singles.show');

    Route::get('/generations', [GenerationController::class, 'index'])->name('generations.index');
    Route::get('/generations/{generation}', [GenerationController::class, 'show'])
        ->whereNumber('generation')
        ->name('generations.show');

    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/teams/{team}', [TeamController::class, 'show'])
        ->whereNumber('team')
        ->name('teams.show');

    Route::get('/captains', [CaptainController::class, 'index'])->name('captains.index');

    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');

    // ---------- Diskografi ----------
    Route::get('/songs', [SongController::class, 'index'])->name('songs.index');
    Route::get('/songs/{song}', [SongController::class, 'show'])
        ->whereNumber('song')->name('songs.show');

    Route::get('/albums', [AlbumController::class, 'index'])->name('albums.index');
    Route::get('/albums/{album}', [AlbumController::class, 'show'])
        ->whereNumber('album')->name('albums.show');

    Route::get('/setlists', [SetlistController::class, 'index'])->name('setlists.index');
    Route::get('/setlists/{setlist}', [SetlistController::class, 'show'])
        ->whereNumber('setlist')->name('setlists.show');

    Route::get('/coupling-songs', [CouplingSongController::class, 'index'])->name('coupling-songs.index');
    Route::get('/coupling-songs/{couplingSong}', [CouplingSongController::class, 'show'])
        ->whereNumber('couplingSong')->name('coupling-songs.show');

    Route::get('/sub-units', [SubUnitController::class, 'index'])->name('sub-units.index');
    Route::get('/sub-units/{subUnit}', [SubUnitController::class, 'show'])
        ->whereNumber('subUnit')->name('sub-units.show');

    Route::get('/mv-locations', [MvLocationController::class, 'index'])->name('mv-locations.index');
    Route::get('/mv-locations/{mvLocation}', [MvLocationController::class, 'show'])
        ->whereNumber('mvLocation')->name('mv-locations.show');
});
