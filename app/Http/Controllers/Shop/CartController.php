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
     */
    public function index()
    {
        $cart = session('cart', []);

        $products = Product::with('category')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = collect($cart)
            ->map(function ($quantity, $productId) use ($products) {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                return [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ];
            })
            ->filter()
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

        $quantity = $data['quantity'] ?? 1;

        $cart = session('cart', []);
        $current = $cart[$product->id] ?? 0;
        $newQuantity = $current + $quantity;

        if ($product->stock > 0) {
            $newQuantity = min($newQuantity, $product->stock);
        }

        $cart[$product->id] = $newQuantity;
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

        $quantity = $data['quantity'];

        if ($product->stock > 0) {
            $quantity = min($quantity, $product->stock);
        }

        $cart[$product->id] = $quantity;
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
