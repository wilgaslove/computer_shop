<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Support\Seo;
use Illuminate\Support\Str;
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
            'seo'         => $this->catalogueSeo($filters, $products, $categories),
            'products'    => $products,
            'categories'  => $categories,
            'filters'     => $filters,
            'priceRange'  => [
                'min' => (float) ($range->min ?? 0),
                'max' => (float) ($range->max ?? 0),
            ],
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

        // Plus forte réduction du catalogue (bannière + description SEO)
        $maxDiscount = (int) round((float) (
            Product::onPromotion()
                ->selectRaw('MAX((price - promo_price) / price * 100) as max_discount')
                ->first()?->max_discount ?? 0
        ));

        return Inertia::render('Shop/Promotions', [
            'seo'         => $this->promotionsSeo($products, $sort, $maxDiscount),
            'products'    => $products,
            'maxDiscount' => $maxDiscount,
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
            'seo'     => Seo::product($product),
            'product' => $product,
        ]);
    }

    /**
     * SEO du catalogue : une page par catégorie est indexée ;
     * recherche, tri et filtres de prix sont en « noindex » (contenu dupliqué).
     */
    private function catalogueSeo(array $filters, $products, $categories): array
    {
        $locality = config('seo.locality');
        $country  = config('seo.country_name');
        $page     = $products->currentPage();
        $category = $filters['category'] !== ''
            ? $categories->firstWhere('id', (int) $filters['category'])
            : null;

        $filtered = $filters['q'] !== ''
            || $filters['min_price'] !== ''
            || $filters['max_price'] !== ''
            || $filters['sort'] !== 'latest'
            || $filters['in_stock'];

        $params = [];
        if ($category) {
            $params['category'] = $category->id;
        }
        if ($page > 1) {
            $params['page'] = $page;
        }

        if ($filters['q'] !== '') {
            $title       = 'Recherche : ' . Str::limit($filters['q'], 40);
            $description = 'Résultats pour « ' . Str::limit($filters['q'], 60) . ' » : ' . $products->total() . ' produit(s) informatique à ' . $locality . '.';
        } elseif ($category) {
            $title       = "{$category->name} à {$locality}, {$country} – prix en FCFA";
            $description = "Découvrez notre sélection de {$category->name} : {$category->products_count} produit(s) disponible(s), prix en FCFA, livraison et paiement à la livraison à {$locality}.";
        } else {
            $title       = "Boutique informatique à {$locality} – ordinateurs, accessoires";
            $description = config('seo.description');
        }

        if ($page > 1) {
            $title .= " – page {$page}";
        }

        $crumbs = [['Accueil', url('/')], ['Boutique', route('shop.products')]];
        if ($category) {
            $crumbs[] = [$category->name, route('shop.products', ['category' => $category->id])];
        }

        return Seo::make($title, $description, [
            'canonical' => route('shop.products', $params),
            'noindex'   => $filtered,
            'jsonld'    => [Seo::breadcrumbs($crumbs)],
        ]);
    }

    private function promotionsSeo($products, string $sort, int $maxDiscount): array
    {
        $page = $products->currentPage();

        $title = $maxDiscount > 0
            ? "Promotions informatique : jusqu'à -{$maxDiscount} %"
            : 'Promotions informatique';

        if ($page > 1) {
            $title .= " – page {$page}";
        }

        return Seo::make(
            $title,
            'Profitez de nos promotions sur les ordinateurs, PC gaming et accessoires'
                . ($maxDiscount > 0 ? " : jusqu'à -{$maxDiscount} %" : '')
                . ' chez ' . Seo::brand() . ', ' . config('seo.locality') . '. Prix en FCFA.',
            [
                'canonical' => route('shop.promotions', $page > 1 ? ['page' => $page] : []),
                // Tri personnalisé ou aucune promo en cours : on évite d'indexer une page vide ou dupliquée
                'noindex'   => $sort !== 'discount' || $products->total() === 0,
            ]
        );
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
