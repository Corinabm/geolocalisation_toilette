<?php

namespace Database\Factories;

use App\Models\Localisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\localisation>
 */
class localisationFactory extends Factory
{
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Localisation::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */


    public function definition(): array
    {
        $locations = [
            [
                'adresse' => 'Front de Mer',
                'latitude' => 14.6033,
                'longitude' => -61.0691,
                'ville' => 'Fort-de-France',
                'code_postal' => 97200,
            ],
            [
                'adresse' => 'Place des Salines',
                'latitude' => 14.4225,
                'longitude' => -60.8756,
                'ville' => 'Sainte-Anne',
                'code_postal' => 97227,
            ],
            [
                'adresse' => 'Rue des Marchands',
                'latitude' => 14.6292,
                'longitude' => -61.0827,
                'ville' => 'Schoelcher',
                'code_postal' => 97233,
            ],
            [
                'adresse' => 'Route de Balata',
                'latitude' => 14.6468,
                'longitude' => -61.0772,
                'ville' => 'Fort-de-France',
                'code_postal' => 97200,
            ],
            [
                'adresse' => 'Port de Pêche',
                'latitude' => 14.7831,
                'longitude' => -61.1804,
                'ville' => 'Le Prêcheur',
                'code_postal' => 97250,
            ],
            [
                'adresse' => 'Place du Marché',
                'latitude' => 14.6644,
                'longitude' => -61.0428,
                'ville' => 'Saint-Joseph',
                'code_postal' => 97212,
            ],
            [
                'adresse' => 'Anse Mitan',
                'latitude' => 14.5445,
                'longitude' => -61.0543,
                'ville' => 'Trois-Îlets',
                'code_postal' => 97229,
            ],
            [
                'adresse' => 'Gare Routière',
                'latitude' => 14.4709,
                'longitude' => -60.8691,
                'ville' => 'Le Marin',
                'code_postal' => 97290,
            ],
            [
                'adresse' => 'Aéroport',
                'latitude' => 14.5969,
                'longitude' => -60.9998,
                'ville' => 'Le Lamentin',
                'code_postal' => 97232,
            ],
            [
                'adresse' => 'Rue de la Liberté',
                'latitude' => 14.7645,
                'longitude' => -61.1211,
                'ville' => 'Le Morne-Rouge',
                'code_postal' => 97260,
            ],
        ];

        return $this->faker->unique()->randomElement($locations);
    }
}
