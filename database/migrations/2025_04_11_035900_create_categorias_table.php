<?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class extends Migration
        {
            public function up()
            {
                Schema::create('categorias', function (Blueprint $table) {
                    $table->id();
                    $table->string('nombre');
                    $table->text('descripcion')->nullable();
                    $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onDelete('cascade');
                    $table->boolean('activo')->default(true);
                    $table->timestamps();
                });
            }

            public function down()
            {
                Schema::dropIfExists('categorias');
            }
        };
