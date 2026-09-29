<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Afficher le contenu du panier.
     * Le panier est resynchronisé avec le catalogue : produit désactivé, supprimé,
     * en rupture ou quantité supérieure au stock => corrigé automatiquement.
     */
    public function index()
    {
        $cart     = session('cart', []);
        $products = Product::with('category')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $cleaned = [];

        foreach ($cart as $productId => $quantity) {
            $product = $products->get($productId);

            if (! $product || ! $product->active || $product->stock <= 0) {
                continue;
            }

            $cleaned[$productId] = min((int) $quantity, $product->stock);
        }

        if ($cleaned !== $cart) {
            session(['cart' => $cleaned]);
            session()->now('error', 'Votre panier a été mis à jour selon la disponibilité des produits.');
        }

        $items = collect($cleaned)
            ->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);

                return [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            })
            ->values();

        return Inertia::render('Shop/Cart/Index', [
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ]);
    }

    /**
     * Ajouter un produit au panier (ou augmenter sa quantité).
     */
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);

        if (! $product->active || $product->stock <= 0) {
            return back()->with('error', 'Ce produit n\'est pas disponible.');
        }

        $cart    = session('cart', []);
        $current = $cart[$product->id] ?? 0;

        if ($current >= $product->stock) {
            return back()->with('error', 'Vous avez déjà la quantité maximale disponible dans votre panier.');
        }

        $cart[$product->id] = min($current + ($data['quantity'] ?? 1), $product->stock);
        session(['cart' => $cart]);

        return back()->with('success', 'Produit ajouté au panier');
    }

    /**
     * Mettre à jour la quantité d'un produit dans le panier.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session('cart', []);

        abort_unless(isset($cart[$product->id]), 404);

        if (! $product->active || $product->stock <= 0) {
            unset($cart[$product->id]);
            session(['cart' => $cart]);

            return back()->with('error', 'Ce produit n\'est plus disponible et a été retiré du panier.');
        }

        $cart[$product->id] = min($data['quantity'], $product->stock);
        session(['cart' => $cart]);

        return back()->with('success', 'Panier mis à jour');
    }

    /**
     * Retirer un produit du panier.
     */
    public function destroy(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produit retiré du panier');
    }

    /**
     * Vider entièrement le panier.
     */
    public function clear()
    {
        session()->forget('cart');

        return back()->with('success', 'Panier vidé');
    }
}
