<?php

namespace App\Policies\Helpers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PolicyHelper
{
    /**
     * Determina si un usuario puede ver un modelo específico basado en su sucursal.
     */
    public static function userCanViewModel(User $user, Model $model): bool
    {
        // Si el usuario es super admin o tiene rol Superadmin, puede ver todo
        if ($user->is_super_admin || $user->hasRole('Superadmin')) {
            return true;
        }
        
        // Si el modelo no tiene relación con sucursal, verificar solo permisos
        if (!method_exists($model, 'sucursal') && !isset($model->sucursal_id)) {
            return true;
        }
        
        // Si el usuario es Administrador, ver todas las sucursales
        if ($user->hasRole('Administrador')) {
            return true;
        }
        
        // Para usuarios normales, verificar si el modelo pertenece a su sucursal
        return $user->sucursal_id === $model->sucursal_id;
    }
}