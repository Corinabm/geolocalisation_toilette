<?php

namespace Database\Seeders;

use App\Models\Toilette;
use App\Models\Localisation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ToiletteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 10 localisations avec leurs toilettes
        Localisation::factory(10)
            ->has(Toilette::factory(1))
            ->create();
    }
}
