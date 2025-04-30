<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up()
        {
            Schema::create('mesas', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->integer('numero_puestos')->default(4);
                $table->foreignId('espacio_id')->constrained()->onDelete('cascade');
                $table->text('descripcion')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        public function down()
        {
            Schema::dropIfExists('mesas');
        }
    };
