<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Liste des commandes avec filtres (statut, paiement, recherche).
     */
    public function index(Request $request)
    {
        $filters = [
            'status'         => $request->query('status'),
            'payment_status' => $request->query('payment_status'),
            'q'              => trim((string) $request->query('q')),
        ];

        $orders = Order::with('user:id,name,email')
            ->withCount('items')
            ->when(
                array_key_exists($filters['status'] ?? '', Order::STATUSES),
                fn ($query) => $query->where('status', $filters['status'])
            )
            ->when(
                array_key_exists($filters['payment_status'] ?? '', Order::PAYMENT_STATUSES),
                fn ($query) => $query->where('payment_status', $filters['payment_status'])
            )
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $term = '%' . $filters['q'] . '%';

                $query->where(function ($q) use ($term) {
                    $q->where('reference', 'like', $term)
                        ->orWhere('shipping_name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhereHas('user', fn ($u) => $u->where('email', 'like', $term));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders'         => $orders,
            'filters'        => $filters,
            'statusCounts'   => Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'statuses'       => $this->options(Order::STATUSES),
            'paymentStatuses' => $this->options(Order::PAYMENT_STATUSES),
        ]);
    }

    /**
     * Détail d'une commande.
     */
    public function show(Order $order)
    {
        $order->load(['items', 'user:id,name,email']);

        return Inertia::render('Admin/Orders/Show', [
            'order'           => $order,
            'transitions'     => collect($order->allowedTransitions())
                ->map(fn ($status) => ['value' => $status, 'label' => Order::STATUSES[$status]])
                ->values(),
            'paymentStatuses' => $this->options(Order::PAYMENT_STATUSES),
        ]);
    }

    /**
     * Changer le statut de la commande.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);

        $new = $data['status'];

        if (! in_array($new, $order->allowedTransitions(), true)) {
            return back()->with('error', 'Ce changement de statut n\'est pas autorisé.');
        }

        // L'annulation remet les quantités en stock.
        if ($new === 'cancelled') {
            return $order->cancelAndRestock()
                ? back()->with('success', 'Commande annulée, stock remis à jour.')
                : back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        $updates = ['status' => $new];

        // Paiement à la livraison : l'argent est encaissé au moment de la livraison.
        if ($new === 'delivered' && $order->payment_method === 'cash_on_delivery') {
            $updates['payment_status'] = 'paid';
        }

        $order->update($updates);

        return back()->with('success', 'Statut mis à jour : ' . Order::STATUSES[$new] . '.');
    }

    /**
     * Marquer la commande comme payée / non payée (ex : réception d'un paiement Mobile Money).
     */
    public function updatePayment(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_status' => ['required', Rule::in(array_keys(Order::PAYMENT_STATUSES))],
        ]);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Impossible de modifier le paiement d\'une commande annulée.');
        }

        $order->update($data);

        return back()->with('success', 'Statut de paiement mis à jour.');
    }

    private function options(array $list): array
    {
        return collect($list)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }
}
