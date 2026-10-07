<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // unique : une même transaction KkiaPay ne peut régler qu'une seule commande
            $table->string('kkiapay_transaction_id')->nullable()->unique()->after('payment_status');
            $table->timestamp('paid_at')->nullable()->after('kkiapay_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['kkiapay_transaction_id']);
            $table->dropColumn(['kkiapay_transaction_id', 'paid_at']);
        });
    }
};
