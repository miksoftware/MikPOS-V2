<?php

    namespace Database\Seeders;

    use App\Models\DianPaymentMethod;
    use Illuminate\Database\Seeder;

    class DianPaymentMethodSeeder extends Seeder
    {
        public function run(): void
        {
            $methods = [
                ['code' => '10', 'description' => 'Efectivo'],
                ['code' => '42', 'description' => 'Consignación'],
                ['code' => '20', 'description' => 'Cheque'],
                ['code' => '46', 'description' => 'Transferencia Débito Interbancario'],
                ['code' => '47', 'description' => 'Transferencia'],
                ['code' => '71', 'description' => 'Bonos'],
                ['code' => '72', 'description' => 'Vales'],
                ['code' => 'ZZZ', 'description' => 'Otro'],
                ['code' => '1', 'description' => 'Medio de pago no definido'],
                ['code' => '49', 'description' => 'Tarjeta Débito'],
                ['code' => '3', 'description' => 'Débito ACH'],
                ['code' => '25', 'description' => 'Cheque certificado'],
                ['code' => '26', 'description' => 'Cheque Local'],
                ['code' => '24', 'description' => 'Nota cambiaria esperando aceptación'],
                ['code' => '64', 'description' => 'Nota promisoria firmada por el banco'],
                ['code' => '65', 'description' => 'Nota promisoria firmada por un banco avalada por otro banco'],
                ['code' => '66', 'description' => 'Nota promisoria firmada'],
                ['code' => '67', 'description' => 'Nota promisoria firmada por un tercero avalada por un banco'],
                ['code' => '2', 'description' => 'Crédito ACH'],
                ['code' => '95', 'description' => 'Giro formato abierto'],
                ['code' => '48', 'description' => 'Tarjeta Crédito'],
                ['code' => '13', 'description' => 'Crédito Ahorro'],
                ['code' => '14', 'description' => 'Débito Ahorro'],
                ['code' => '39', 'description' => 'Crédito Intercambio Corporativo (CTX)'],
            ];

            foreach ($methods as $method) {
                DianPaymentMethod::create($method);
            }
        }
    }
