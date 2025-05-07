<?php

          use Illuminate\Database\Migrations\Migration;
          use Illuminate\Database\Schema\Blueprint;
          use Illuminate\Support\Facades\Schema;

          return new class extends Migration
          {
              public function up(): void
              {
                  Schema::create('compras', function (Blueprint $table) {
                      $table->id();
                      $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
                      $table->foreignId('proveedor_id')->constrained('suppliers')->onDelete('cascade');
                      $table->string('numero_factura')->nullable();
                      $table->enum('estado', ['pendiente', 'completada', 'anulada'])->default('pendiente');
                      $table->decimal('subtotal', 12, 2)->default(0);
                      $table->decimal('impuestos', 12, 2)->default(0);
                      $table->decimal('total', 12, 2)->default(0);
                      $table->text('observaciones')->nullable();
                      $table->date('fecha_compra');
                      $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
                      $table->timestamps();
                  });
              }

              public function down(): void
              {
                  Schema::dropIfExists('compras');
              }
          };
