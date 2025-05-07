<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'sucursal_id',
        'categoria_id',
        'impuesto_id',
        'aplica_impuesto',
        'tiene_descuento',
        'tipo_descuento',
        'valor_descuento',
        'precio_costo',
        'precio_venta',
        'utilidad_porcentaje',
        'utilidad_monto',
        'activo',
        'compuesto',
        'tipo_inventario',
        'stock_actual',
        'stock_minimo',
        'produccion_diaria',
        'controlar_stock',
    ];

    protected $casts = [
        'aplica_impuesto' => 'boolean',
        'tiene_descuento' => 'boolean',
        'activo' => 'boolean',
        'compuesto' => 'boolean',
        'controlar_stock' => 'boolean',
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'utilidad_porcentaje' => 'decimal:2',
        'utilidad_monto' => 'decimal:2',
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'produccion_diaria' => 'decimal:2',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function impuesto(): BelongsTo
    {
        return $this->belongsTo(Impuesto::class);
    }

    public function ingredientes(): BelongsToMany
    {
        return $this->belongsToMany(Ingrediente::class, 'producto_ingrediente')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function productosCombo(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'producto_combo', 'combo_id', 'producto_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function detallesCompra()
    {
        return $this->morphMany(DetalleCompra::class, 'comprable');
    }

}
