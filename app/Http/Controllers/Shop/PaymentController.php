<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\KkiapayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Page qui ouvre le widget KkiaPay pour une commande en attente de paiement.
     */
    public function show(Request $request, Order $order, KkiapayService $kkiapay)
    {
        $this->authorizeOwner($request, $order);

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        if ($order->payment_method !== 'kkiapay' || $order->status !== 'pending') {
            return redirect()
                ->route('account.orders.show', $order)
                ->with('error', 'Cette commande ne peut pas être payée en ligne.');
        }

        if (! $kkiapay->isConfigured()) {
            return redirect()
                ->route('account.orders.show', $order)
                ->with('error', 'Le paiement en ligne n\'est pas disponible pour le moment.');
        }

        return Inertia::render('Shop/Checkout/Pay', [
            'order'    => [
                'reference' => $order->reference,
                'total'     => $order->total,
            ],
            'amount'   => (int) round((float) $order->total),
            'kkiapay'  => [
                'key'     => $kkiapay->publicKey(),   // clé PUBLIQUE uniquement
                'sandbox' => $kkiapay->isSandbox(),
            ],
            'customer' => [
                'name'  => $order->shipping_name,
                'email' => $request->user()->email,
                'phone' => $this->localPhone($order->phone),
            ],
        ]);
    }

    /**
     * Appelé par la page après le succès du widget : on vérifie le paiement auprès de KkiaPay
     * avant de marquer la commande comme payée.
     */
    public function confirm(Request $request, Order $order, KkiapayService $kkiapay)
    {
        $this->authorizeOwner($request, $order);

        $data = $request->validate([
            'transaction_id' => ['required', 'string', 'max:100'],
        ]);

        $transactionId = trim($data['transaction_id']);

        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        abort_unless($order->payment_method === 'kkiapay', 404);

        // Une transaction ne peut régler qu'une seule commande (anti-réutilisation).
        $alreadyUsed = Order::where('kkiapay_transaction_id', $transactionId)
            ->where('id', '!=', $order->id)
            ->exists();

        if ($alreadyUsed) {
            Log::warning('KkiaPay : transaction déjà utilisée pour une autre commande', [
                'order'       => $order->reference,
                'transaction' => $transactionId,
            ]);

            return back()->with('error', 'Cette transaction est déjà associée à une autre commande.');
        }

        $transaction = $kkiapay->verify($transactionId);

        if (! $transaction) {
            return back()->with(
                'error',
                'Nous n\'avons pas pu vérifier votre paiement pour le moment. Réessayez dans un instant : '
                . 'si vous avez été débité, la commande sera confirmée automatiquement.'
            );
        }

        if (! $kkiapay->isSuccessfulFor($transaction, $order)) {
            return back()->with('error', 'Le paiement n\'a pas été confirmé par KkiaPay. Vous n\'avez pas été débité ou le montant est incorrect.');
        }

        $order->markPaidOnline($transactionId);

        if ($order->status === 'cancelled') {
            // Paiement reçu après annulation (ex : commande expirée) : à rembourser ou à traiter à la main.
            Log::warning('KkiaPay : paiement reçu sur une commande annulée', [
                'order'       => $order->reference,
                'transaction' => $transactionId,
            ]);
        }

        return redirect()
            ->route('checkout.success', $order)
            ->with('success', 'Paiement reçu, merci !');
    }

    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 404);
    }

    /** Numéro sans indicatif ni séparateurs, pour préremplir le widget (le client peut le modifier). */
    private function localPhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        $digits = preg_replace('/^(00)?229/', '', $digits);

        return $digits ?? '';
    }
}
