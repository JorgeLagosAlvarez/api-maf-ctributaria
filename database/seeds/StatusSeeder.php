<?php

use App\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::create([
            'name' => 'Ingresado'
        ]);

        Status::create([
            'name' => 'Procesado'
        ]);

        Status::create([
            'name' => 'Eliminado'
        ]);
    }
}
