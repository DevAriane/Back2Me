<?php

namespace Database\Seeders;

use App\Models\Niveau;
use Illuminate\Database\Seeder;

class NiveauSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveaux = [
            'L1',
            'L2',
            'L3',
            'M1',
            'M2',
            'Doctorat',
            'Autre',
        ];

        foreach ($niveaux as $name) {
            Niveau::query()->firstOrCreate(['name' => $name]);
        }
    }
}
