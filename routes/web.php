<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToiletteController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/toilettes',[ToiletteController::class, 'index']);
