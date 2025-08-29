<?php

namespace App\Http\Controllers;

use App\Models\Toilette;
use Illuminate\Http\Request;

class ToiletteController extends Controller
{
    public function index()
    {
        /*$toilettes = Toilette::all(); 
        dd($toilettes); */

        /* retourne la vue Blade qui contient la map */
        return view('toilettes.index');

    }

    /**
     * Fournit les données des toilettes sous forme d'API JSON.
     */
    public function api(Request $request)
    {
        // Récupère toutes les toilettes avec leurs localisations associées.
        // On utilise la méthode 'with()' pour éviter le problème du N+1 (chargement des relations en une seule requête).
        $toilettes = Toilette::with('localisation')->get();

        // Retourne les données au format JSON.
        return response()->json($toilettes);
    }

}
