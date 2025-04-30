<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            ['nombre' => 'AMAZONAS', 'codigo' => '91'],
            ['nombre' => 'ANTIOQUIA', 'codigo' => '05'],
            ['nombre' => 'ARAUCA', 'codigo' => '81'],
            ['nombre' => 'ATLÁNTICO', 'codigo' => '08'],
            ['nombre' => 'BOGOTÁ, D.C.', 'codigo' => '11'],
            ['nombre' => 'BOLÍVAR', 'codigo' => '13'],
            ['nombre' => 'BOYACÁ', 'codigo' => '15'],
            ['nombre' => 'CALDAS', 'codigo' => '17'],
            ['nombre' => 'CAQUETÁ', 'codigo' => '18'],
            ['nombre' => 'CASANARE', 'codigo' => '85'],
            ['nombre' => 'CAUCA', 'codigo' => '19'],
            ['nombre' => 'CESAR', 'codigo' => '20'],
            ['nombre' => 'CHOCÓ', 'codigo' => '27'],
            ['nombre' => 'CÓRDOBA', 'codigo' => '23'],
            ['nombre' => 'CUNDINAMARCA', 'codigo' => '25'],
            ['nombre' => 'GUAINÍA', 'codigo' => '94'],
            ['nombre' => 'GUAVIARE', 'codigo' => '95'],
            ['nombre' => 'HUILA', 'codigo' => '41'],
            ['nombre' => 'LA GUAJIRA', 'codigo' => '44'],
            ['nombre' => 'MAGDALENA', 'codigo' => '47'],
            ['nombre' => 'META', 'codigo' => '50'],
            ['nombre' => 'NARIÑO', 'codigo' => '52'],
            ['nombre' => 'NORTE DE SANTANDER', 'codigo' => '54'],
            ['nombre' => 'PUTUMAYO', 'codigo' => '86'],
            ['nombre' => 'QUINDÍO', 'codigo' => '63'],
            ['nombre' => 'RISARALDA', 'codigo' => '66'],
            ['nombre' => 'SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA', 'codigo' => '88'],
            ['nombre' => 'SANTANDER', 'codigo' => '68'],
            ['nombre' => 'SUCRE', 'codigo' => '70'],
            ['nombre' => 'TOLIMA', 'codigo' => '73'],
            ['nombre' => 'VALLE DEL CAUCA', 'codigo' => '76'],
            ['nombre' => 'VAUPÉS', 'codigo' => '97'],
            ['nombre' => 'VICHADA', 'codigo' => '99'],
        ];
        foreach ($departamentos as $departamento) {
            Departamento::create($departamento);
        }
    }
}
