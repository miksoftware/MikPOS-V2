<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'sucursal_id',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_super_admin' => 'boolean',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Verifica si el usuario pertenece a una sucursal específica
     */
    public function belongsToSucursal(?int $sucursalId): bool
    {
        if ($this->is_super_admin) {
            return true;
        }
        
        if ($sucursalId === null || $this->sucursal_id === null) {
            return false;
        }
        
        return $this->sucursal_id === $sucursalId;
    }

    /**
     * Verifica si el usuario puede ver datos de una sucursal específica
     */
    public function canViewSucursal(?int $sucursalId): bool
    {
        if ($sucursalId === null) {
            return false;
        }
        
        if ($this->is_super_admin || $this->hasRole('Superadmin')) {
            return true;
        }
        
        return $this->hasRole('Administrador') ||
            $this->can('view_any_sucursal') ||
            $this->belongsToSucursal($sucursalId);
    }
}