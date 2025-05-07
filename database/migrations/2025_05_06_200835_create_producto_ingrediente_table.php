<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {
        public function up(): void
        {
            Schema::create('producto_ingrediente', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
                $table->foreignId('ingrediente_id')->constrained('ingredientes');
                $table->decimal('cantidad', 10, 3);
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('producto_ingrediente');
        }
    };
