<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Modificar el ENUM para añadir 'combo'
        DB::statement("ALTER TABLE productos MODIFY COLUMN tipo_inventario ENUM('producto_terminado', 'elaborado_bajo_pedido', 'produccion_limitada', 'combo') DEFAULT 'elaborado_bajo_pedido'");

        // Crear la tabla pivote para la relación de combos
        Schema::create('producto_combo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Eliminar la tabla pivote
        Schema::dropIfExists('producto_combo');

        // Revertir el ENUM a su estado original
        DB::statement("ALTER TABLE productos MODIFY COLUMN tipo_inventario ENUM('producto_terminado', 'elaborado_bajo_pedido', 'produccion_limitada') DEFAULT 'elaborado_bajo_pedido'");
    }
};
