<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\KkiapayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Notification serveur de KkiaPay. Filet de sécurité : si le client ferme son navigateur
 * juste après avoir payé, la commande est quand même marquée comme payée.
 */
class KkiapayWebhookController extends Controller
{
    public function __invoke(Request $request, KkiapayService $kkiapay): JsonResponse
    {
        // 1) Authenticité : le secret configuré dans le dashboard KkiaPay doit correspondre.
        $secret = (string) config('services.kkiapay.webhook_secret');
        $header = (string) $request->header('x-kkiapay-secret', '');

        if ($secret === '' || ! hash_equals($secret, $header)) {
            Log::warning('KkiaPay webhook rejeté : secret invalide ou absent');

            return response()->json(['message' => 'Forbidden'], 403);
        }

        $payload       = $request->all();
        $transactionId = (string) ($payload['transactionId'] ?? '');

        if ($transactionId === '') {
            return response()->json(['message' => 'Ignoré : pas de transaction']);
        }

        // 2) On ne croit pas le contenu : on redemande l'état réel à KkiaPay.
        $transaction = $kkiapay->verify($transactionId);

        if (! $transaction) {
            // 503 : KkiaPay pourra renvoyer la notification plus tard.
            return response()->json(['message' => 'Vérification impossible'], 503);
        }

        // 3) Retrouver la commande (la référence a été envoyée au widget : partnerId / data).
        $order = Order::whereIn('reference', $this->referenceCandidates($payload, $transaction))->first();

        if (! $order || $order->payment_method !== 'kkiapay') {
            Log::warning('KkiaPay webhook : aucune commande correspondante', ['transaction' => $transactionId]);

            return response()->json(['message' => 'Commande introuvable']);
        }

        if (! $kkiapay->isSuccessfulFor($transaction, $order)) {
            return response()->json(['message' => 'Paiement non abouti : aucune action']);
        }

        $usedElsewhere = Order::where('kkiapay_transaction_id', $transactionId)
            ->where('id', '!=', $order->id)
            ->exists();

        if ($usedElsewhere) {
            Log::warning('KkiaPay webhook : transaction déjà utilisée', ['transaction' => $transactionId]);

            return response()->json(['message' => 'Transaction déjà utilisée']);
        }

        $order->markPaidOnline($transactionId); // idempotent : sans effet si déjà payée

        if ($order->status === 'cancelled') {
            Log::warning('KkiaPay webhook : paiement reçu sur une commande annulée', [
                'order'       => $order->reference,
                'transaction' => $transactionId,
            ]);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * Valeurs pouvant contenir la référence de commande (partnerId, stateData…).
     */
    private function referenceCandidates(array $payload, array $transaction): array
    {
        $values = [];

        array_walk_recursive($payload, function ($v, $k) use (&$values) {
            if (is_scalar($v) && in_array($k, ['partnerId', 'stateData', 'data', 'reference'], true)) {
                $values[] = (string) $v;
            }
        });

        array_walk_recursive($transaction, function ($v, $k) use (&$values) {
            if (is_scalar($v) && in_array($k, ['partnerId', 'stateData', 'data', 'reference'], true)) {
                $values[] = (string) $v;
            }
        });

        return array_values(array_unique(array_filter($values)));
    }
}
