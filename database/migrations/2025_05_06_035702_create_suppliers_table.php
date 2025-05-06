<?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            return new class extends Migration
            {
                public function up(): void
                {
                    Schema::create('suppliers', function (Blueprint $table) {
                        $table->id();
                        $table->string('name');
                        $table->string('company_name')->nullable();
                        $table->string('identification_type')->nullable();
                        $table->string('identification_number')->nullable()->unique();
                        $table->string('address')->nullable();
                        $table->string('phone')->nullable();
                        $table->string('email')->nullable();
                        $table->string('contact_person')->nullable();
                        $table->string('contact_phone')->nullable();
                        $table->decimal('credit_limit', 15, 2)->default(0);
                        $table->integer('credit_days')->default(0);
                        $table->boolean('is_active')->default(true);
                        $table->text('notes')->nullable();
                        $table->timestamps();
                    });
                }

                public function down(): void
                {
                    Schema::dropIfExists('suppliers');
                }
            };
