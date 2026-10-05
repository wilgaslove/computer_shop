<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Support\Seo;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Page Contact (publique).
     * Un client connecté voit en plus ses dernières commandes pour contacter le service client à leur sujet.
     * On peut aussi arriver avec ?order=CS-XXXXXX-XXXXXX (bouton depuis « Mes commandes »).
     */
    public function show(Request $request)
    {
        $user   = $request->user();
        $orders = collect();
        $selectedOrder = null;

        if ($user) {
            $orders = $user->orders()
                ->with('items:id,order_id,product_name')
                ->latest()
                ->limit(5)
                ->get();

            $requested = (string) $request->query('order', '');

            if ($requested !== '') {
                $found = $user->orders()
                    ->with('items:id,order_id,product_name')
                    ->where('reference', $requested)
                    ->first();

                if ($found) {
                    $selectedOrder = $found->reference;

                    // La commande demandée doit apparaître dans la liste même si elle est plus ancienne.
                    if (! $orders->contains('id', $found->id)) {
                        $orders->prepend($found);
                    }
                }
            }
        }

        return Inertia::render('Shop/Contact', [
            'seo' => Seo::contact(),
            'contact'       => SiteContent::get('shop'),
            'faq'           => SiteContent::get('home')['faq'],
            'orders'        => $orders->map(fn (Order $order) => $this->orderSummary($order))->values(),
            'selectedOrder' => $selectedOrder,
            'defaults'      => [
                'name'  => $user?->name ?? '',
                'email' => $user?->email ?? '',
            ],
        ]);
    }

    /**
     * Enregistre le message. Fonctionne pour un visiteur comme pour un client connecté.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:120'],
            'email'      => ['required', 'email', 'max:190'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'subject'    => ['required', 'string', 'max:190'],
            'message'    => ['required', 'string', 'min:10', 'max:5000'],
            'order'      => ['nullable', 'string', 'max:50'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:4096'],
        ]);

        $user  = $request->user();
        $order = null;

        // On ne rattache une commande que si elle appartient bien au client connecté.
        if (! empty($data['order'])) {
            $order = $user?->orders()->where('reference', $data['order'])->first();

            if (! $order) {
                throw ValidationException::withMessages([
                    'order' => 'Cette commande est introuvable dans votre compte.',
                ]);
            }
        }

        // Disque « local » (privé) forcé : ne dépend pas de FILESYSTEM_DISK, qui vaut « public » dans ce projet.
        // Seule l'équipe peut télécharger le fichier, via le dashboard.
        $path = $request->hasFile('attachment')
            ? $request->file('attachment')->store('contact-attachments', 'local')
            : null;

        ContactMessage::create([
            'user_id'    => $user?->id,
            'order_id'   => $order?->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => $data['subject'],
            'message'    => $data['message'],
            'attachment' => $path,
            'status'     => 'new',
        ]);

        return back()->with('success', 'Message envoyé. Notre équipe vous répondra très vite.');
    }

    private function orderSummary(Order $order): array
    {
        $names = $order->items->pluck('product_name');
        $extra = $names->count() - 2;

        return [
            'reference'    => $order->reference,
            'summary'      => $names->take(2)->implode(' + ') . ($extra > 0 ? " + {$extra} autre(s)" : ''),
            'status'       => $order->status,
            'status_label' => $order->status_label,
            'created_at'   => $order->created_at,
        ];
    }
}
