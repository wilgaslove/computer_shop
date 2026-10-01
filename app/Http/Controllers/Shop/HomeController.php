<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeroSlider;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\SiteContent;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $content  = SiteContent::get('home');
        $products = $content['products'];

        return Inertia::render('Shop/HomePage', [
            'content'  => $content,
            'sliders'  => HeroSlider::where('active', true)->orderBy('position')->orderBy('id')->get(),

            'newProducts' => $products['new_enabled']
                ? Product::with(['images', 'category'])
                    ->where('active', true)
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->limit($this->count($products['new_count']))
                    ->get()
                : [],

            'promoProducts' => $products['promo_enabled']
                ? Product::with(['images', 'category'])
                    ->onPromotion()
                    ->orderByRaw('(price - promo_price) / price DESC')
                    ->limit($this->count($products['promo_count']))
                    ->get()
                : [],

            'popularProducts' => $products['popular_enabled']
                ? $this->popularProducts($this->count($products['popular_count']))
                : [],

            // Pour les raccourcis « parcourir par catégorie »
            'categories' => Category::withCount([
                    'products as products_count' => fn ($q) => $q->where('active', true),
                ])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->limit(12)
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Produits actifs les plus commandés (commandes annulées exclues).
     * Vide tant qu'il n'y a pas eu de ventes : la section se masque alors toute seule.
     */
    private function popularProducts(int $limit): Collection
    {
        $ids = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->whereNotNull('order_items.product_id')
            ->groupBy('order_items.product_id')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit($limit * 2) // marge : certains produits peuvent être désactivés depuis
            ->pluck('order_items.product_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (! $ids) {
            return collect();
        }

        return Product::with(['images', 'category'])
            ->where('active', true)
            ->whereIn('id', $ids)
            ->get()
            ->sortBy(fn (Product $p) => array_search($p->id, $ids, true))
            ->take($limit)
            ->values();
    }

    private function count(mixed $value): int
    {
        return max(1, min(12, (int) $value));
    }
}
