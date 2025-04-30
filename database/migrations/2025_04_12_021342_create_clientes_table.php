<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up()
        {
            Schema::create('clientes', function (Blueprint $table) {
                $table->id();
                $table->enum('tipo_cliente', ['natural', 'juridico']);
                $table->foreignId('tipo_documento_id')->constrained('tipo_documentos');
                $table->string('numero_documento');
                $table->string('digito_verificacion')->nullable();
                $table->string('nombres')->nullable(); // Para cliente natural
                $table->string('apellidos')->nullable(); // Para cliente natural
                $table->string('razon_social')->nullable(); // Para cliente jurídico
                $table->string('telefono')->nullable();
                $table->string('email')->nullable();
                $table->foreignId('departamento_id')->constrained('departamentos');
                $table->foreignId('municipio_id')->constrained('municipios');
                $table->string('direccion')->nullable();
                $table->boolean('tiene_credito')->default(false);
                $table->decimal('cupo_credito', 15, 2)->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();

                // Asegurar que no haya documentos duplicados del mismo tipo
                $table->unique(['tipo_documento_id', 'numero_documento']);
            });
        }

        public function down()
        {
            Schema::dropIfExists('clientes');
        }
    };
