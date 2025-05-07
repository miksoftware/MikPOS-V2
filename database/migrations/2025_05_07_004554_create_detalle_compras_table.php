<?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                public function up(): void
                {
                    Schema::create('detalle_compras', function (Blueprint $table) {
                        $table->id();
                        $table->foreignId('compra_id')->constrained()->onDelete('cascade');
                        $table->morphs('comprable'); // Para permitir Producto o Ingrediente
                        $table->decimal('cantidad', 10, 2);
                        $table->decimal('precio_compra_anterior', 12, 2)->default(0);
                        $table->decimal('precio_compra_actual', 12, 2);
                        $table->decimal('precio_venta_anterior', 12, 2)->default(0);
                        $table->decimal('precio_venta_nuevo', 12, 2)->nullable();
                        $table->boolean('actualizar_precio_venta')->default(false);
                        $table->decimal('subtotal', 12, 2);
                        $table->timestamps();
                    });
                }

                public function down(): void
                {
                    Schema::dropIfExists('detalle_compras');
                }
            };
