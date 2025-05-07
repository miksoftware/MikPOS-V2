<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'proveedor_id',
        'numero_factura',
        'estado',
        'subtotal',
        'impuestos',
        'total',
        'observaciones',
        'fecha_compra',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleCompra::class);
    }

    // Método para calcular los totales
    public function calcularTotales(): void
    {
        $this->subtotal = $this->detalles->sum('subtotal');
        // Podrías agregar impuestos si es necesario
        $this->total = $this->subtotal + $this->impuestos;
        $this->save();
    }
}
