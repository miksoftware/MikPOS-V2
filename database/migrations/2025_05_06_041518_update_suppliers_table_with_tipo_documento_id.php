<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::table('suppliers', function (Blueprint $table) {
                // Eliminamos la columna antigua
                $table->dropColumn('identification_type');

                // Añadimos la nueva relación
                $table->foreignId('tipo_documento_id')
                      ->after('company_name')
                      ->constrained('tipo_documentos');
            });
        }

        public function down(): void
        {
            Schema::table('suppliers', function (Blueprint $table) {
                // Restauramos la columna antigua
                $table->string('identification_type')->nullable()->after('company_name');

                // Eliminamos la relación
                $table->dropConstrainedForeignId('tipo_documento_id');
            });
        }
    };
