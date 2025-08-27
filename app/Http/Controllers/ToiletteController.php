<?php

namespace App\Http\Controllers;

use App\Models\Toilette;
use Illuminate\Http\Request;

class ToiletteController extends Controller
{
    public function index()
    {
        $toilettes = Toilette::all(); 
        dd($toilettes); 
    }

}
