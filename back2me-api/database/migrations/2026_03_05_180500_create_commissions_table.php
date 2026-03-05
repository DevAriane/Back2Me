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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->unique()->constrained()->onDelete('cascade');
            $table->foreignId('objet_id')->constrained()->onDelete('cascade');
            $table->foreignId('finder_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('claimer_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('object_price', 12, 2);
            $table->decimal('commission_total', 12, 2);
            $table->decimal('finder_commission', 12, 2);
            $table->decimal('supervisor_commission', 12, 2);
            $table->decimal('app_commission', 12, 2);
            $table->enum('payout_status', ['accrued', 'paid'])->default('accrued');
            $table->timestamp('paid_out_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
