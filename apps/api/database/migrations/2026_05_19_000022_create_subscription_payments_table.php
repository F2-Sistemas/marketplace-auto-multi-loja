<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method'); // credit_card, boleto, pix
            $table->string('status')->default('paid'); // paid, pending, failed
            $table->timestamp('billing_date');
            $table->string('receipt_url')->nullable();
            $table->timestamps();

            $table->index('store_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
