<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HaltecApiSettings extends Settings
{
    public string $haltec_user;
    public string $haltec_password;
    public string $haltec_client_id;
    public string $haltec_client_secret;

    // Añadimos credenciales para el entorno de pruebas
    public string $haltec_user_test;
    public string $haltec_password_test;
    public string $haltec_client_id_test;
    public string $haltec_client_secret_test;

    // Añadimos URLs para producción y pruebas
    public string $haltec_api_url_production;
    public string $haltec_api_url_test;

    // Selector de entorno activo
    public string $haltec_environment;

    public static function group(): string
    {
        return 'haltec';
    }
}
