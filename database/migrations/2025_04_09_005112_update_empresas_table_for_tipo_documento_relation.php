<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Eliminar la columna antigua si existe
            if (Schema::hasColumn('empresas', 'tipo_documento')) {
                $table->dropColumn('tipo_documento');
            }
            
            // Agregar la nueva columna para la relación
            $table->foreignId('tipo_documento_id')->after('id')->constrained('tipo_documentos');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['tipo_documento_id']);
            $table->dropColumn('tipo_documento_id');
            $table->string('tipo_documento')->after('id')->nullable();
        });
    }
};