<?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                public function up(): void
                {
                    Schema::create('ingredientes', function (Blueprint $table) {
                        $table->id();
                        $table->string('nombre');
                        $table->text('descripcion')->nullable();
                        $table->foreignId('sucursal_id')->constrained('sucursales');
                        $table->foreignId('unidad_medida_id')->constrained('unidad_medidas');
                        $table->foreignId('area_preparacion_id')->constrained('area_preparaciones');
                        $table->foreignId('impuesto_id')->nullable()->constrained('impuestos');
                        $table->decimal('stock_actual', 10, 2)->default(0);
                        $table->decimal('stock_minimo', 10, 2)->default(0);
                        $table->boolean('aplica_impuesto')->default(false);
                        $table->boolean('tiene_descuento')->default(false);
                        $table->enum('tipo_descuento', ['porcentaje', 'monto'])->nullable();
                        $table->decimal('valor_descuento', 10, 2)->nullable();
                        $table->decimal('precio_compra', 10, 2);
                        $table->decimal('precio_venta', 10, 2);
                        $table->decimal('utilidad_porcentaje', 10, 2)->nullable();
                        $table->decimal('utilidad_monto', 10, 2)->nullable();
                        $table->boolean('activo')->default(true);
                        $table->timestamps();
                    });
                }

                public function down(): void
                {
                    Schema::dropIfExists('ingredientes');
                }
            };
