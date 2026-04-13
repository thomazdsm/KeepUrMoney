<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('competences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->noActionOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->enum('status', ['future', 'current', 'consolidated'])->default('future');
            $table->decimal('total_income_planned', 15, 2)->default(0);
            $table->decimal('total_expense_planned', 15, 2)->default(0);
            $table->decimal('total_income_realized', 15, 2)->default(0);
            $table->decimal('total_expense_realized', 15, 2)->default(0);
            $table->timestamps();

            // Garante que o usuário não tenha duas competências para o mesmo mês/ano
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competences');
    }
};
