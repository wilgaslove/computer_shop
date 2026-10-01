<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HeroSlider;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Options de tri disponibles : clé => [colonne, sens].
     */
    private const SORTS = [
        'latest'     => ['created_at', 'desc'],
        'price_asc'  => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'name_asc'   => ['name', 'asc'],
    ];

    /**
     * Catalogue public : recherche, catégorie, prix, disponibilité, tri, pagination.
     */
    public function index(Request $request)
    {
        $filters    = $this->filters($request);
        $hasFilters = $filters['q'] !== ''
            || $filters['category'] !== ''
            || $filters['min_price'] !== ''
            || $filters['max_price'] !== ''
            || $filters['sort'] !== 'latest'
            || $filters['in_stock'];

        [$column, $direction] = self::SORTS[$filters['sort']];

        $products = Product::with(['images', 'category'])
            ->where('active', true)
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                // Chaque mot doit se retrouver dans le nom ou la description.
                foreach (preg_split('/\s+/', $filters['q']) as $word) {
                    $like = '%' . addcslashes($word, '\\%_') . '%';

                    $query->where(function ($q) use ($like) {
                        $q->where('name', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
                }
            })
            ->when($filters['category'] !== '', fn ($query) => $query->where('category_id', $filters['category']))
            ->when($filters['min_price'] !== '', fn ($query) => $query->where('price', '>=', $filters['min_price']))
            ->when($filters['max_price'] !== '', fn ($query) => $query->where('price', '<=', $filters['max_price']))
            ->when($filters['in_stock'], fn ($query) => $query->where('stock', '>', 0))
            ->orderBy($column, $direction)
            ->orderBy('id', 'desc') // ordre stable entre les pages
            ->paginate(12)
            ->withQueryString();

        $categories = Category::withCount([
                'products as products_count' => fn ($q) => $q->where('active', true),
            ])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        $range = Product::where('active', true)
            ->selectRaw('MIN(price) as min, MAX(price) as max')
            ->first();

        return Inertia::render('Shop/Products/Index', [
            'products'    => $products,
            'categories'  => $categories,
            'filters'     => $filters,
            'priceRange'  => [
                'min' => (float) ($range->min ?? 0),
                'max' => (float) ($range->max ?? 0),
            ],
            // La bannière n'apparaît que sur le catalogue « nu », pas sur une recherche.
            'heroSliders' => $hasFilters
                ? []
                : HeroSlider::where('active', true)->orderBy('position')->orderBy('id')->get(),
        ]);
    }

    /**
     * Page « Promotions » : uniquement les produits actifs dont le prix promo est renseigné.
     */
    public function promotions(Request $request)
    {
        $sorts = [
            'discount'   => 'Meilleures réductions',
            'price_asc'  => 'Prix croissant',
            'price_desc' => 'Prix décroissant',
            'latest'     => 'Nouveautés',
        ];

        $sort = $request->query('sort');
        $sort = (is_string($sort) && isset($sorts[$sort])) ? $sort : 'discount';

        $query = Product::with(['images', 'category'])->onPromotion();

        match ($sort) {
            // Pourcentage de réduction, du plus fort au plus faible
            'discount'   => $query->orderByRaw('(price - promo_price) / price DESC'),
            'price_asc'  => $query->orderBy('promo_price', 'asc'),
            'price_desc' => $query->orderBy('promo_price', 'desc'),
            'latest'     => $query->orderBy('created_at', 'desc'),
        };

        $products = $query
            ->orderBy('id', 'desc') // ordre stable entre les pages
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Shop/Promotions', [
            'products'    => $products,
            // Plus forte réduction du catalogue (affichée dans la bannière)
            'maxDiscount' => (int) round((float) (
                Product::onPromotion()
                    ->selectRaw('MAX((price - promo_price) / price * 100) as max_discount')
                    ->first()?->max_discount ?? 0
            )),
            'sort'        => $sort,
            'sorts'    => collect($sorts)
                ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
                ->values(),
        ]);
    }

    public function show(Product $product)
    {
        abort_if(! $product->active, 404);

        $product->load(['category', 'images']);

        return Inertia::render('Shop/Products/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Nettoie les paramètres d'URL. Une valeur invalide est ignorée
     * (jamais d'erreur ni de redirection sur une simple page de catalogue).
     */
    private function filters(Request $request): array
    {
        $q = mb_substr(trim((string) $request->query('q', '')), 0, 100);

        $category = $request->query('category');
        $category = (is_scalar($category) && ctype_digit((string) $category)) ? (string) $category : '';

        $min = $this->price($request->query('min_price'));
        $max = $this->price($request->query('max_price'));

        // Bornes inversées : on les remet dans l'ordre.
        if ($min !== '' && $max !== '' && (float) $min > (float) $max) {
            [$min, $max] = [$max, $min];
        }

        $sort = $request->query('sort');
        $sort = (is_string($sort) && isset(self::SORTS[$sort])) ? $sort : 'latest';

        return [
            'q'         => $q,
            'category'  => $category,
            'min_price' => $min,
            'max_price' => $max,
            'sort'      => $sort,
            'in_stock'  => $request->boolean('in_stock'),
        ];
    }

    private function price(mixed $value): string
    {
        if (! is_scalar($value) || ! is_numeric($value) || (float) $value < 0) {
            return '';
        }

        return (string) (float) $value;
    }
}
