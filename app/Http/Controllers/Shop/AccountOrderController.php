<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
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
            // Une commande déjà payée en ligne ne s'annule pas seule : il faut un remboursement.
            'canCancel' => $order->status === 'pending' && $order->payment_status === 'unpaid',
            'canPay'    => $order->status === 'pending'
                && $order->payment_status === 'unpaid'
                && $order->payment_method === 'kkiapay',
        ]);
    }

    /**
     * Annulation par le client, possible tant que la commande est « en attente ».
     * Le stock des produits est remis à jour.
     */
    public function cancel(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        // Le client ne peut annuler que tant que la commande est « en attente ».
        if ($order->status !== 'pending' || $order->payment_status !== 'unpaid' || ! $order->cancelAndRestock()) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        return back()->with('success', 'Votre commande a été annulée.');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        // 404 plutôt que 403 : on ne révèle pas l'existence des commandes des autres clients.
        abort_unless($order->user_id === $request->user()->id, 404);
    }
}
