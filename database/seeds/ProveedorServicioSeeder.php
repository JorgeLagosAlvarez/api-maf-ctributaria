<?php

use App\ProveedorServicio;
use Illuminate\Database\Seeder;

class ProveedorServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProveedorServicio::create([
            'nombre_empresa' => 'Metrogas S.A',
            'nombre_fantasia' => 'Metrogas',
            'rut' => '96722460-K',
            'direccion' => 'El Regidor 54',
            'ciudad' => 'Las Condes',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Chilquinta Energia S.A',
            'nombre_fantasia' => 'Chilquinta',
            'rut' => '96813520-1',
            'direccion' => 'Av Argentina N1, Piso 9',
            'ciudad' => 'Valparaíso',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Compañía General de Electricidad S.A',
            'nombre_fantasia' => 'CGE',
            'rut' => '76411321-7',
            'direccion' => 'Presidente Riesco 5561, Piso 17',
            'ciudad' => 'Las Condes',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Enel Distribución Chile S.A',
            'nombre_fantasia' => 'Enel',
            'rut' => '96800570-7',
            'direccion' => 'Santa Rosa 76, Piso 8',
            'ciudad' => 'Santiago',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Empresas de Servicio Sanitarios de Los Lagos S.A',
            'nombre_fantasia' => 'Sv Sanitario Los Lagos',
            'rut' => '96579800-5',
            'direccion' => 'Covadonga 52',
            'ciudad' => 'Puerto Montt',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Aguas del Valle S.A',
            'nombre_fantasia' => 'Aguas del Valle',
            'rut' => '99541380-9',
            'direccion' => 'Cochrane 751',
            'ciudad' => 'Valparaíso',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Esval S.A',
            'nombre_fantasia' => 'Esval',
            'rut' => '76000739-0',
            'direccion' => 'Cochrane 751',
            'ciudad' => 'Valparaíso',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Essbio',
            'nombre_fantasia' => 'Essbio',
            'rut' => '76833300-9',
            'direccion' => 'Arturo Prat 199, Of 1501',
            'ciudad' => 'Concepción',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Aguas Andinas',
            'nombre_fantasia' => 'Aguas Andina',
            'rut' => '61808000-5',
            'direccion' => 'Av Presidente Balmaceda 1398',
            'ciudad' => 'Santiago',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Aguas Araucania S.A',
            'nombre_fantasia' => 'Aguas Araucanía',
            'rut' => '76215637-7',
            'direccion' => 'Isidora Goyenechea 3600',
            'ciudad' => 'Las Condes',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Empresa Nacional de Telecomunicaciones S.A',
            'nombre_fantasia' => 'ENTEL',
            'rut' => '92580000-7',
            'direccion' => 'Costanera Sur 2201, Piso 22',
            'ciudad' => 'Las Condes',
        ]);

        ProveedorServicio::create([
            'nombre_empresa' => 'Telefónica Chile S.A',
            'nombre_fantasia' => 'MOVISTAR',
            'rut' => '90635000-9',
            'direccion' => 'Av Providencia 111',
            'ciudad' => 'Providencia',
        ]);
    }
}
