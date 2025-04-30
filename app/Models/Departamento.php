<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'codigo'];
    
    public function municipios(): HasMany
    {
        return $this->hasMany(Municipio::class);
    }
    
    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class);
    }
    
    public function sucursales(): HasMany
    {
        return $this->hasMany(Sucursal::class);
    }
}