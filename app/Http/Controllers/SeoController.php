<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    /**
     * /sitemap.xml : la liste des pages à indexer, mise en cache 1 heure.
     */
    public function sitemap()
    {
        $xml = Cache::remember('sitemap_xml', 3600, function () {
            $urls = [
                ['loc' => url('/'),                    'priority' => '1.0', 'freq' => 'daily'],
                ['loc' => route('shop.products'),      'priority' => '0.9', 'freq' => 'daily'],
                ['loc' => route('shop.promotions'),    'priority' => '0.8', 'freq' => 'daily'],
                ['loc' => route('contact'),            'priority' => '0.5', 'freq' => 'monthly'],
            ];

            Category::withCount(['products as products_count' => fn ($q) => $q->where('active', true)])
                ->having('products_count', '>', 0)
                ->get(['id'])
                ->each(function ($category) use (&$urls) {
                    $urls[] = [
                        'loc'      => route('shop.products', ['category' => $category->id]),
                        'priority' => '0.8',
                        'freq'     => 'weekly',
                    ];
                });

            Product::where('active', true)
                ->orderBy('id')
                ->get(['id', 'updated_at'])
                ->each(function ($product) use (&$urls) {
                    $urls[] = [
                        'loc'      => route('shop.products.show', $product->id),
                        'lastmod'  => $product->updated_at?->toAtomString(),
                        'priority' => '0.7',
                        'freq'     => 'weekly',
                    ];
                });

            $out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            foreach ($urls as $u) {
                $out .= "  <url>\n    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
                if (! empty($u['lastmod'])) {
                    $out .= "    <lastmod>{$u['lastmod']}</lastmod>\n";
                }
                $out .= "    <changefreq>{$u['freq']}</changefreq>\n    <priority>{$u['priority']}</priority>\n  </url>\n";
            }

            return $out . '</urlset>' . "\n";
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * /robots.txt : généré pour contenir l'adresse réelle du sitemap.
     * (Supprimez public/robots.txt, sinon ce fichier statique a la priorité.)
     */
    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /account',
            'Disallow: /profile',
            'Disallow: /login',
            'Disallow: /register',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
