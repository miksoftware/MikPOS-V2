<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DianPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'description'];

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class, 'dian_payment_method_id');
    }
}
