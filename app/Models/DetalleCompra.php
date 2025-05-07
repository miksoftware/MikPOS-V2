<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id',
        'comprable_id',
        'comprable_type',
        'cantidad',
        'precio_compra_anterior',
        'precio_compra_actual',
        'precio_venta_anterior',
        'precio_venta_nuevo',
        'actualizar_precio_venta',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_compra_anterior' => 'decimal:2',
        'precio_compra_actual' => 'decimal:2',
        'precio_venta_anterior' => 'decimal:2',
        'precio_venta_nuevo' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'actualizar_precio_venta' => 'boolean',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function comprable(): MorphTo
    {
        return $this->morphTo();
    }

    // Método para calcular el subtotal
    public function calcularSubtotal(): void
    {
        $this->subtotal = $this->cantidad * $this->precio_compra_actual;
        $this->save();
    }
}
