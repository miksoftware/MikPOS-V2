<?php

     use Illuminate\Database\Migrations\Migration;
     use Illuminate\Database\Schema\Blueprint;
     use Illuminate\Support\Facades\Schema;

     return new class extends Migration
     {
         public function up(): void
         {
             Schema::create('payment_methods', function (Blueprint $table) {
                 $table->id();
                 $table->string('name');
                 $table->string('description')->nullable();
                 $table->boolean('is_active')->default(true);
                 $table->foreignId('dian_payment_method_id')
                       ->constrained()
                       ->onDelete('restrict');
                 $table->timestamps();
             });
         }

         public function down(): void
         {
             Schema::dropIfExists('payment_methods');
         }
     };
