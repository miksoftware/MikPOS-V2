<?php

namespace App\Providers;

use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Illuminate\Support\ServiceProvider;

class ShieldServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Sobrescribe el grupo de navegación después de que Shield lo haya registrado
        RoleResource::navigationGroup('Configuración');
    }
}
