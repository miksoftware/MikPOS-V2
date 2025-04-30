<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Sucursal;
use App\Models\User;

class FilamentSucursalPolicyProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Políticas para Sucursales
        Gate::define('view-sucursal', function (User $user, Sucursal $sucursal) {
            return $user->canViewSucursal($sucursal->id);
        });

        Gate::define('update-sucursal', function (User $user, Sucursal $sucursal) {
            return $user->hasRole('super_admin') || 
                   $user->can('edit_sucursal') && $user->belongsToSucursal($sucursal->id);
        });

        Gate::define('delete-sucursal', function (User $user, Sucursal $sucursal) {
            return $user->hasRole('super_admin') || $user->can('delete_sucursal');
        });
    }
}