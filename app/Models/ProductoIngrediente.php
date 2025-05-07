<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductoIngrediente extends Pivot
{
    protected $table = 'producto_ingrediente';

    protected $fillable = [
        'producto_id',
        'ingrediente_id',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
    ];
}
