<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid {--minutes=60 : Âge minimum de la commande}';

    protected $description = 'Annule les commandes à payer en ligne restées impayées et remet les produits en stock';

    public function handle(): int
    {
        $count = 0;

        Order::where('payment_method', 'kkiapay')
            ->where('payment_status', 'unpaid')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes((int) $this->option('minutes')))
            ->each(function (Order $order) use (&$count) {
                if ($order->cancelAndRestock()) {
                    $count++;
                }
            });

        $this->info("{$count} commande(s) impayée(s) annulée(s).");

        return self::SUCCESS;
    }
}
