<?php

namespace Database\Seeders;

use App\Models\Filiere;
use Illuminate\Database\Seeder;

class FiliereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filieres = [
            'Informatique',
            'Réseaux et télécoms',
            'Génie logiciel',
            'Comptabilité',
            'Finance',
            'Marketing',
            'Gestion',
            'Droit',
            'Autre',
        ];

        foreach ($filieres as $name) {
            Filiere::query()->firstOrCreate(['name' => $name]);
        }
    }
}
