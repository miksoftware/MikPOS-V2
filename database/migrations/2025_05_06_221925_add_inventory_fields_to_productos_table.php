<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::table('productos', function (Blueprint $table) {
                $table->enum('tipo_inventario', [
                    'producto_terminado',
                    'elaborado_bajo_pedido',
                    'produccion_limitada'
                ])->default('elaborado_bajo_pedido');
                $table->decimal('stock_actual', 10, 2)->default(0);
                $table->decimal('stock_minimo', 10, 2)->default(0);
                $table->decimal('produccion_diaria', 10, 2)->default(0);
                $table->boolean('controlar_stock')->default(true);
            });
        }

        public function down(): void
        {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn([
                    'tipo_inventario',
                    'stock_actual',
                    'stock_minimo',
                    'produccion_diaria',
                    'controlar_stock'
                ]);
            });
        }
    };
