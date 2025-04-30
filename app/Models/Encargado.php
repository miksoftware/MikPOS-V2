<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encargado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'numero_documento',
    ];

    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }
}