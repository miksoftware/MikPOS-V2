<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('impuesto_id')->nullable()->constrained('impuestos');
            $table->boolean('aplica_impuesto')->default(false);
            $table->boolean('tiene_descuento')->default(false);
            $table->enum('tipo_descuento', ['porcentaje', 'monto'])->nullable();
            $table->decimal('valor_descuento', 10, 2)->nullable();
            $table->decimal('precio_costo', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('utilidad_porcentaje', 10, 2)->nullable();
            $table->decimal('utilidad_monto', 10, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('compuesto')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
