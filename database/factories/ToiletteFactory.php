<?php

namespace Database\Factories;

use App\Models\Toilette;
use App\Models\Localisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Toilette>
 */
class ToiletteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Toilette::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        $etats = ['ouvert', 'fermé'];
        $noms = [
            'Toilettes du Front de Mer',
            'Toilettes de la Place des Salines',
            'Toilettes du Marché de Schoelcher',
            'Toilettes du Jardin de Balata',
            'Toilettes du Port de Pêche',
            'Toilettes du Centre-Ville',
            'Toilettes de la Plage de l\'Anse Mitan',
            'Toilettes de la Gare Routière',
            'Toilettes de l\'Aéroport Aimé Césaire',
            'Toilettes de la Bibliothèque Municipale',
        ];

        return [
            'nom' => $this->faker->unique()->randomElement($noms),
            'horaires' => $this->faker->time('H:i') . ' - ' . $this->faker->time('H:i'),
            'etat' => $this->faker->randomElement($etats),
            'image' => 'images/toilettes-' . $this->faker->slug . '.jpg',
            'localisation_id' => Localisation::factory(),
        ];
    }
}
