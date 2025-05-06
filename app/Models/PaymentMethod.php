<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active', 'dian_payment_method_id'];

    public function dianPaymentMethod(): BelongsTo
    {
        return $this->belongsTo(DianPaymentMethod::class);
    }
}
