<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Le produit peut être supprimé plus tard : on garde l'historique de la commande.
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Instantané au moment de l'achat
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
