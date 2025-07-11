<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('dian_payment_methods', function (Blueprint $table) {
                $table->id();
                $table->string('code');
                $table->string('description');
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('dian_payment_methods');
        }
    };
