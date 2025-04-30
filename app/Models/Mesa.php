<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'numero_puestos',
        'espacio_id',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'numero_puestos' => 'integer',
    ];

    public function espacio(): BelongsTo
    {
        return $this->belongsTo(Espacio::class);
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    // Esta relación ayudará a filtrar por sucursal aunque no exista campo directo
    public function scopeSucursal($query, $sucursal_id)
    {
        return $query->whereHas('espacio', function($query) use ($sucursal_id) {
            $query->where('sucursal_id', $sucursal_id);
        });
    }
}
