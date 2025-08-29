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

     /**
     * Fournit les données des toilettes les plus proches sous forme d'API JSON.
     */
    public function getProches(Request $request)
    {
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        if (!$userLat || !$userLng) {
            return response()->json(['error' => 'Coordonnées manquantes'], 400);
        }

        // Rayon de la Terre en kilomètres
        $earthRadius = 6371;

        // La formule de Haversine pour calculer la distance
        $haversine = "(
            $earthRadius * acos(
                cos(radians(?))
                * cos(radians(localisations.latitude))
                * cos(radians(localisations.longitude) - radians(?))
                + sin(radians(?))
                * sin(radians(localisations.latitude))
            )
        )";

        $toilettes = Toilette::select(
                'toilettes.*',
                DB::raw("$haversine AS distance")
            )
            ->join('localisations', 'toilettes.localisation_id', '=', 'localisations.id')
            ->with('localisation')
            ->orderBy('distance', 'asc') // Tri par distance croissante
            ->limit(50) // Limite les résultats pour éviter la surcharge
            ->setBindings([$userLat, $userLng, $userLat]) // Associe les variables aux '?'
            ->get();

        return response()->json($toilettes);
    }

}
