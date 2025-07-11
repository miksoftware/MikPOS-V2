<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('module')->nullable()->after('name');
            $table->text('description')->nullable()->after('module');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('guard_name');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['module', 'description']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};