<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToiletteController;

/*Route::get('/', function () {
    return view('welcome');
});

Route::get('/toilettes',[ToiletteController::class, 'index']);*/

/* affiche la page principale */
Route::get('/',[ToiletteController::class, 'index'])->name('toilettes.index');

/* fournir les données des toilettes au format json */
Route::get('/api/toilettes', [ToiletteController::class, 'api'])->name('toilettes.api');

Route::get('/api/toilettes-proches', [ToiletteController::class, 'getProches'])->name('toilettes.proches');
