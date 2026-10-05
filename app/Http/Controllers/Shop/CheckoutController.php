<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    /**
     * Page de validation de commande.
     */
    public function index(Request $request)
    {
        $items = $this->cartItems();

        if ($items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        return Inertia::render('Shop/Checkout/Index', [
            'items'          => $items,
            'total'          => $items->sum('subtotal'),
            'paymentMethods' => collect(Order::PAYMENT_METHODS)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->values(),
            'defaults'       => [
                'shipping_name' => $request->user()->name,
            ],
        ]);
    }

    /**
     * Création de la commande à partir du panier (session).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'shipping_name'  => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:30'],
            'city'           => ['required', 'string', 'max:100'],
            'address'        => ['required', 'string', 'max:500'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Tout ou rien : si une ligne échoue, rien n'est créé et le stock reste intact.
        $order = DB::transaction(function () use ($cart, $data, $request) {

            // Verrouille les produits pour éviter que deux clients achètent le dernier article.
            $products = Product::whereIn('id', array_keys($cart))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lines = [];
            $total = 0;

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);

                if (! $product || ! $product->active) {
                    throw ValidationException::withMessages([
                        'cart' => "Un produit de votre panier n'est plus disponible. Veuillez vérifier votre panier.",
                    ]);
                }

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stock insuffisant pour « {$product->name} » (disponible : {$product->stock}).",
                    ]);
                }

                $subtotal = $product->current_price * $quantity;
                $total   += $subtotal;

                $lines[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_image' => $product->cover_image,
                    'price'         => $product->current_price,
                    'quantity'      => $quantity,
                    'subtotal'      => $subtotal,
                ];

                $product->decrement('stock', $quantity);
            }

            $order = Order::create([
                'reference'      => Order::generateReference(),
                'user_id'        => $request->user()->id,
                'status'         => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'total'          => $total,
                'shipping_name'  => $data['shipping_name'],
                'phone'          => $data['phone'],
                'city'           => $data['city'],
                'address'        => $data['address'],
                'notes'          => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($lines);

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('checkout.success', $order);
    }

    /**
     * Page de confirmation après commande.
     */
    public function success(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');

        return Inertia::render('Shop/Checkout/Success', [
            'order' => $order,
        ]);
    }

    /**
     * Lignes du panier (produits actifs uniquement), pour l'affichage.
     */
    private function cartItems()
    {
        $cart = session('cart', []);

        $products = Product::whereIn('id', array_keys($cart))
            ->where('active', true)
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                return [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->current_price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }
}
