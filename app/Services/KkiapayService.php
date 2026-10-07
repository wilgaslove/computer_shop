<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Vérification des paiements KkiaPay côté serveur.
 * Les clés privées ne quittent jamais le serveur : seule la clé publique est envoyée au navigateur.
 */
class KkiapayService
{
    public function isConfigured(): bool
    {
        return filled(config('services.kkiapay.public_key'))
            && filled(config('services.kkiapay.private_key'))
            && filled(config('services.kkiapay.secret'));
    }

    public function isSandbox(): bool
    {
        return (bool) config('services.kkiapay.sandbox');
    }

    public function publicKey(): string
    {
        return (string) config('services.kkiapay.public_key');
    }

    private function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://api-sandbox.kkiapay.me'
            : 'https://api.kkiapay.me';
    }

    /**
     * Interroge KkiaPay pour connaître l'état réel d'une transaction.
     * On ne fait jamais confiance au navigateur ni au contenu brut du webhook.
     * Retourne null si KkiaPay est injoignable ou répond en erreur.
     */
    public function verify(string $transactionId): ?array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key'     => (string) config('services.kkiapay.public_key'),
                'x-private-key' => (string) config('services.kkiapay.private_key'),
                'x-secret-key'  => (string) config('services.kkiapay.secret'),
            ])
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->post($this->baseUrl() . '/api/v1/transactions/status', [
                    'transactionId' => $transactionId,
                ]);
        } catch (\Throwable $e) {
            Log::error('KkiaPay : vérification impossible', [
                'transaction' => $transactionId,
                'error'       => $e->getMessage(),
            ]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('KkiaPay : réponse en erreur lors de la vérification', [
                'transaction' => $transactionId,
                'http_status' => $response->status(),
                'body'        => Str::limit($response->body(), 500),
            ]);

            return null;
        }

        return $response->json();
    }

    /**
     * La transaction est réussie ET couvre bien le montant de la commande.
     * (>= : si les frais sont à la charge du client, le montant débité peut être supérieur.)
     */
    public function isSuccessfulFor(array $transaction, Order $order): bool
    {
        return strtoupper((string) ($transaction['status'] ?? '')) === 'SUCCESS'
            && (float) ($transaction['amount'] ?? 0) >= (float) $order->total;
    }
}
