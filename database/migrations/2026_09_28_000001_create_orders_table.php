<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('pending');          // pending, confirmed, shipped, delivered, cancelled
            $table->string('payment_method');                       // cash_on_delivery, mobile_money
            $table->string('payment_status')->default('unpaid');    // unpaid, paid

            $table->decimal('total', 12, 2);

            // Livraison
            $table->string('shipping_name');
            $table->string('phone', 30);
            $table->string('city', 100);
            $table->text('address');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
