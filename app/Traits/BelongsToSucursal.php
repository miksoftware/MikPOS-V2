<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToSucursal
{
    protected static function booted()
    {
        parent::booted();
        
        static::addGlobalScope('sucursal', function (Builder $builder) {
            // Solo aplicar el alcance cuando estamos en una solicitud web con un usuario autenticado
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }
            
            $user = Auth::user();
            
            if ($user && !$user->is_super_admin && !$user->hasRole(['Superadmin', 'Administrador'])) {
                $builder->where('sucursal_id', $user->sucursal_id);
            }
        });
        
        static::creating(function (Model $model) {
            // Solo aplicar cuando estamos en una solicitud web con un usuario autenticado
            if (app()->runningInConsole() && !app()->runningUnitTests()) {
                return;
            }
            
            $user = Auth::user();
            
            if ($user && !$user->is_super_admin && !$user->hasRole(['Superadmin', 'Administrador']) && is_null($model->sucursal_id)) {
                $model->sucursal_id = $user->sucursal_id;
            }
        });
    }

    public function sucursal()
    {
        return $this->belongsTo(\App\Models\Sucursal::class);
    }
}