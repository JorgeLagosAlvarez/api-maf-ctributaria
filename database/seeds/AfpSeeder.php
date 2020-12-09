<?php

use App\Afp;
use Illuminate\Database\Seeder;

class AfpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Afp::create([
            'name' => 'Capital'
        ]);

        Afp::create([
            'name' => 'Cuprum'
        ]);

        Afp::create([
            'name' => 'Habitat'
        ]);

        Afp::create([
            'name' => 'Modelo'
        ]);

        Afp::create([
            'name' => 'PlanVital'
        ]);

        Afp::create([
            'name' => 'Provida'
        ]);
    }
}
