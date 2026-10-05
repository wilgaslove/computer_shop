<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Construit les données SEO d'une page : titre, description, canonical, robots,
 * Open Graph et données structurées (JSON-LD).
 * Elles sont rendues côté serveur dans resources/views/app.blade.php,
 * donc visibles par Google et par les aperçus de partage sans exécuter de JavaScript.
 */
class Seo
{
    /** Pages privées ou techniques : jamais indexées. */
    private const PRIVATE_PATHS = [
        'admin*', 'cart*', 'checkout*', 'account*', 'profile*',
        'login', 'register', 'forgot-password', 'reset-password*',
        'verify-email*', 'confirm-password', 'email/*',
    ];

    public static function brand(): string
    {
        $brand = trim((string) (SiteContent::get('shop')['brand'] ?? ''));

        return $brand !== '' ? $brand : (string) config('app.name');
    }

    /**
     * @param array{canonical?:string,image?:string,type?:string,noindex?:bool,suffix?:bool,jsonld?:array} $options
     */
    public static function make(?string $title = null, ?string $description = null, array $options = []): array
    {
        $brand  = self::brand();
        $suffix = $options['suffix'] ?? true;

        $fullTitle = ($title === null || $title === '')
            ? $brand
            : ($suffix ? "{$title} | {$brand}" : $title);

        $description = self::clean($description ?? (string) config('seo.description'));

        return [
            'title'       => $fullTitle,
            'description' => Str::limit($description, 160, '…'),
            'canonical'   => $options['canonical'] ?? url()->current(),
            'image'       => self::absolute($options['image'] ?? config('seo.og_image')),
            'type'        => $options['type'] ?? 'website',
            'robots'      => ($options['noindex'] ?? false)
                ? 'noindex, follow'
                : 'index, follow, max-image-preview:large, max-snippet:-1',
            'site_name'   => $brand,
            'locale'      => config('seo.og_locale', 'fr_FR'),
            'jsonld'      => array_values(array_filter($options['jsonld'] ?? [])),
        ];
    }

    /** SEO par défaut de toute page : les pages privées sont en noindex. */
    public static function forRequest(Request $request): array
    {
        return self::make(null, null, [
            'noindex' => $request->is(...self::PRIVATE_PATHS),
        ]);
    }

    public static function home(): array
    {
        $tagline = SiteContent::get('shop')['tagline'] ?? 'Matériel informatique';

        return self::make(
            self::brand() . ' – ' . $tagline . ' à ' . config('seo.locality') . ', ' . config('seo.country_name'),
            config('seo.description'),
            [
                'suffix'    => false,
                'canonical' => url('/'),
                'jsonld'    => [self::business(), self::website()],
            ]
        );
    }

    public static function contact(): array
    {
        return self::make(
            'Contact – ' . config('seo.locality'),
            'Contactez ' . self::brand() . ' à ' . config('seo.locality')
                . ' : téléphone, WhatsApp, e-mail, adresse et horaires d\'ouverture.',
            [
                'canonical' => route('contact'),
                'jsonld'    => [self::business()],
            ]
        );
    }

    public static function product(Product $product): array
    {
        $brand    = self::brand();
        $locality = config('seo.locality');
        $category = $product->category?->name;
        $price    = $product->current_price;
        $priceTxt = self::fcfa($price);
        $inStock  = $product->stock > 0;

        $title = Str::limit($product->name, 50, '…') . ' – ' . $priceTxt;
        if ($product->is_on_promotion) {
            $title .= ' (-' . $product->discount_percent . ' %)';
        }

        $plain = self::clean($product->description);
        $intro = "{$product->name} à {$priceTxt}"
            . ($category ? " – {$category}" : '')
            . " chez {$brand}, {$locality}.";
        $description = $intro . ' ' . ($plain !== ''
            ? $plain
            : ($inStock ? 'En stock. Livraison et paiement à la livraison.' : 'Actuellement en rupture de stock.'));

        $images = collect([$product->cover_image])
            ->merge($product->images->pluck('path'))
            ->filter()
            ->unique()
            ->take(5)
            ->map(fn ($path) => self::absolute('/storage/' . ltrim($path, '/')))
            ->values()
            ->all();

        $url = route('shop.products.show', $product->id);

        $crumbs = [['Accueil', url('/')], ['Boutique', route('shop.products')]];
        if ($category) {
            $crumbs[] = [$category, route('shop.products', ['category' => $product->category_id])];
        }
        $crumbs[] = [$product->name, $url];

        return self::make($title, $description, [
            'canonical' => $url,
            'image'     => $images[0] ?? null,
            'type'      => 'product',
            'jsonld'    => [
                array_filter([
                    '@context'    => 'https://schema.org',
                    '@type'       => 'Product',
                    'name'        => $product->name,
                    'description' => Str::limit($plain !== '' ? $plain : $intro, 300, '…'),
                    'image'       => $images ?: null,
                    'sku'         => 'P-' . $product->id,
                    'category'    => $category,
                    'offers'      => [
                        '@type'         => 'Offer',
                        'url'           => $url,
                        'priceCurrency' => config('seo.currency'),
                        'price'         => number_format($price, 2, '.', ''),
                        'availability'  => $inStock
                            ? 'https://schema.org/InStock'
                            : 'https://schema.org/OutOfStock',
                        'seller'        => ['@type' => 'Organization', 'name' => $brand],
                    ],
                ]),
                self::breadcrumbs($crumbs),
            ],
        ]);
    }

    /** @param array<int, array{0:string,1:string}> $crumbs [[nom, url], ...] */
    public static function breadcrumbs(array $crumbs): array
    {
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => collect($crumbs)->values()->map(fn ($crumb, $i) => [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $crumb[0],
                'item'     => $crumb[1],
            ])->all(),
        ];
    }

    /** La boutique : adresse, téléphone, horaires… (alimente la fiche locale dans Google). */
    public static function business(): array
    {
        $shop   = SiteContent::get('shop');
        $sameAs = array_values(array_filter([$shop['facebook'] ?? null, $shop['instagram'] ?? null]));

        return array_filter([
            '@context'           => 'https://schema.org',
            '@type'              => 'ComputerStore',
            '@id'                => url('/') . '#boutique',
            'name'               => self::brand(),
            'url'                => url('/'),
            'logo'               => url('/images/logo-navbar.png'),
            'image'              => self::absolute(config('seo.og_image')),
            'telephone'          => $shop['phone'] ?? null,
            'email'              => $shop['email'] ?? null,
            'address'            => array_filter([
                '@type'           => 'PostalAddress',
                'streetAddress'   => $shop['address'] ?? null,
                'addressLocality' => config('seo.locality'),
                'addressCountry'  => config('seo.country'),
            ]),
            'geo'                => (! empty($shop['map_lat']) && ! empty($shop['map_lng'])) ? [
                '@type'     => 'GeoCoordinates',
                'latitude'  => (float) $shop['map_lat'],
                'longitude' => (float) $shop['map_lng'],
            ] : null,
            'openingHours'       => config('seo.opening_hours'),
            'currenciesAccepted' => config('seo.currency'),
            'paymentAccepted'    => 'Espèces, Mobile Money',
            'sameAs'             => $sameAs ?: null,
        ]);
    }

    /** Active la barre de recherche du site dans les résultats Google. */
    public static function website(): array
    {
        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            'name'            => self::brand(),
            'url'             => url('/'),
            'inLanguage'      => 'fr',
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => route('shop.products') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function fcfa(float|int|string $value): string
    {
        return number_format((float) $value, 0, ',', ' ') . ' FCFA';
    }

    private static function clean(?string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $text)));
    }

    private static function absolute(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://']) ? $path : url($path);
    }
}
