<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_cliente',
        'tipo_documento_id',
        'numero_documento',
        'digito_verificacion',
        'nombres',
        'apellidos',
        'razon_social',
        'telefono',
        'email',
        'departamento_id',
        'municipio_id',
        'direccion',
        'tiene_credito',
        'cupo_credito',
        'activo',
    ];

    protected $casts = [
        'tiene_credito' => 'boolean',
        'activo' => 'boolean',
        'cupo_credito' => 'decimal:2',
    ];

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(Municipio::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        if ($this->tipo_cliente === 'natural') {
            return "{$this->nombres} {$this->apellidos}";
        }

        return $this->razon_social;
    }
}
