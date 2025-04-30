<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SucursalScopeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Añadir macro para consultas con restricción de sucursal
        Builder::macro('restrictBySucursal', function () {
            /** @var Builder $this */
            $user = Auth::user();
            
            // Si no hay usuario autenticado, no mostrar nada
            if (!$user) {
                return $this->where('id', 0);
            }
            
            // Si es super admin o tiene permiso para ver todas las sucursales
            if ($user->hasRole('super_admin') || $user->can('view_any_sucursal')) {
                return $this;
            }
            
            // Para usuarios asociados a una sucursal
            if ($user->sucursal_id) {
                // Detectar si estamos consultando la tabla sucursales
                $model = $this->getModel();
                
                if ($model instanceof Sucursal) {
                    // Restricción directa: sólo ver su propia sucursal
                    return $this->where('id', $user->sucursal_id);
                } elseif (method_exists($model, 'sucursal')) {
                    // Restricción por relación: filtrar por la sucursal del usuario
                    return $this->where('sucursal_id', $user->sucursal_id);
                }
            }
            
            // Por defecto, no mostrar nada si no cumple los criterios
            return $this->where('id', 0);
        });
    }
}