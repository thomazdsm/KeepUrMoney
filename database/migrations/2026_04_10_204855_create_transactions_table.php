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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competence_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            // Nullable pois o dinheiro pode ainda não ter saído/entrado de lugar nenhum (Planejado)
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('credit_card_invoice_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->string('description');
            $table->date('due_date'); // Data prevista
            $table->decimal('planned_amount', 15, 2);

            // Dados da baixa (Realizado)
            $table->date('realized_date')->nullable();
            $table->decimal('realized_amount', 15, 2)->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->boolean('is_fixed')->default(false); // Veio de uma recorrência?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
