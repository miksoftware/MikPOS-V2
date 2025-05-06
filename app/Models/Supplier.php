<?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Database\Eloquent\Relations\BelongsTo;

        class Supplier extends Model
        {
            use HasFactory;

            protected $fillable = [
                'name',
                'company_name',
                'tipo_documento_id',
                'identification_number',
                'address',
                'phone',
                'email',
                'contact_person',
                'contact_phone',
                'credit_limit',
                'credit_days',
                'is_active',
                'notes',
            ];

            protected $casts = [
                'credit_limit' => 'decimal:2',
                'credit_days' => 'integer',
                'is_active' => 'boolean',
            ];

            public function tipoDocumento(): BelongsTo
            {
                return $this->belongsTo(TipoDocumento::class);
            }
        }
