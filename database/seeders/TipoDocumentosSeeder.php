<?php

namespace Database\Seeders;

use App\Models\TipoDocumento;
use Illuminate\Database\Seeder;

class TipoDocumentosSeeder extends Seeder
{
    public function run(): void
    {
        $tiposDocumento = [
            [
                'nombre' => 'Registro civil',
                'codigo' => '1',
                'descripcion' => 'Registro civil de nacimiento'
            ],
            [
                'nombre' => 'Tarjeta de identidad',
                'codigo' => '2',
                'descripcion' => 'Tarjeta de identidad'
            ],
            [
                'nombre' => 'Cédula de ciudadanía',
                'codigo' => '3',
                'descripcion' => 'Documento de identidad nacional para ciudadanos colombianos'
            ],
            [
                'nombre' => 'Tarjeta de extranjería',
                'codigo' => '4',
                'descripcion' => 'Documento de identidad para extranjeros'
            ],
            [
                'nombre' => 'Cédula de extranjería',
                'codigo' => '5',
                'descripcion' => 'Documento de identidad para extranjeros residentes'
            ],
            [
                'nombre' => 'NIT',
                'codigo' => '6',
                'descripcion' => 'Número de Identificación Tributaria'
            ],
            [
                'nombre' => 'Pasaporte',
                'codigo' => '7',
                'descripcion' => 'Documento de viaje internacional'
            ],
            [
                'nombre' => 'Documento de identificación extranjero',
                'codigo' => '8',
                'descripcion' => 'Documento de identidad expedido en otros países'
            ],
            [
                'nombre' => 'PEP',
                'codigo' => '9',
                'descripcion' => 'Permiso Especial de Permanencia para ciudadanos venezolanos'
            ],            
            [
                'nombre' => 'NIT de otro país',
                'codigo' => '10',
                'descripcion' => 'Número de identificación tributaria de otro país'
            ],
            [
                'nombre' => 'NUIP',
                'codigo' => '11',
                'descripcion' => 'Número Único de Identificación Personal'
            ],
            [
                'nombre' => 'PPT',
                'codigo' => '92',
                'descripcion' => 'Permiso por Protección Temporal para ciudadanos venezolanos'
            ],
            [
                'nombre' => 'Registro NIUP',
                'codigo' => '15',
                'descripcion' => 'Número Único de Identificación Personal generado por la Registraduría'
            ],
            [
                'nombre' => 'Visa',
                'codigo' => '43',
                'descripcion' => 'Identificación de visas'
            ],
            [
                'nombre' => 'TFNR',
                'codigo' => '24',
                'descripcion' => 'Tarjeta fiscal número único de afiliación'
            ],
            [
                'nombre' => 'Sin identificación del exterior o para uso definido por la DIAN',
                'codigo' => '00',
                'descripcion' => 'Documento para casos especiales definidos por la DIAN'
            ],
        ];

        foreach ($tiposDocumento as $tipo) {
            TipoDocumento::create($tipo);
        }
    }
}