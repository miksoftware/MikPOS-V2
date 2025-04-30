<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento');
            $table->string('numero_registro')->unique();
            $table->string('razon_social');
            $table->string('codigo_ciiu')->nullable();
            $table->string('giro_empresa');
            $table->string('telefono');
            $table->string('email');
            $table->foreignId('departamento_id')->constrained('departamentos');
            $table->string('direccion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};