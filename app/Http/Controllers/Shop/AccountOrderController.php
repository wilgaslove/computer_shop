<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AccountOrderController extends Controller
{
    /**
     * Liste des commandes du client connecté.
     */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return Inertia::render('Shop/Account/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Détail d'une commande (uniquement la sienne).
     */
    public function show(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        $order->load('items');

        return Inertia::render('Shop/Account/Orders/Show', [
            'order'     => $order,
            'canCancel' => $order->status === 'pending',
        ]);
    }

    /**
     * Annulation par le client, possible tant que la commande est « en attente ».
     * Le stock des produits est remis à jour.
     */
    public function cancel(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        if ($order->status !== 'pending') {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        DB::transaction(function () use ($order) {
            // Verrouille la commande : évite une double annulation (double clic) qui rendrait le stock deux fois.
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            if ($locked->status !== 'pending') {
                return;
            }

            foreach ($locked->items as $item) {
                if ($item->product_id) {
                    $item->product()->increment('stock', $item->quantity);
                }
            }

            $locked->update(['status' => 'cancelled']);
        });

        return back()->with('success', 'Votre commande a été annulée.');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        // 404 plutôt que 403 : on ne révèle pas l'existence des commandes des autres clients.
        abort_unless($order->user_id === $request->user()->id, 404);
    }
}
