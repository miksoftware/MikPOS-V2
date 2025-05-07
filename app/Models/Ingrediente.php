<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingrediente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'sucursal_id',
        'unidad_medida_id',
        'area_preparacion_id',
        'impuesto_id',
        'stock_actual',
        'stock_minimo',
        'aplica_impuesto',
        'tiene_descuento',
        'tipo_descuento',
        'valor_descuento',
        'precio_compra',
        'precio_venta',
        'utilidad_porcentaje',
        'utilidad_monto',
        'activo',
    ];

    protected $casts = [
        'aplica_impuesto' => 'boolean',
        'tiene_descuento' => 'boolean',
        'activo' => 'boolean',
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'utilidad_porcentaje' => 'decimal:2',
        'utilidad_monto' => 'decimal:2',
    ];

    // Añadimos la relación con sucursal
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function areaPreparacion(): BelongsTo
    {
        return $this->belongsTo(AreaPreparacion::class);
    }

    public function impuesto(): BelongsTo
    {
        return $this->belongsTo(Impuesto::class);
    }

    public function detallesCompra()
    {
        return $this->morphMany(DetalleCompra::class, 'comprable');
    }
}
