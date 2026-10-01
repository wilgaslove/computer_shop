<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteContentController extends Controller
{
    /**
     * Page d'édition : contenu de l'accueil + coordonnées / pied de page.
     */
    public function edit()
    {
        return Inertia::render('Admin/SiteContent/Edit', [
            'home' => SiteContent::get('home'),
            'shop' => SiteContent::get('shop'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        // validated() ne contient que les champs déclarés dans les règles : rien d'autre ne peut être enregistré.
        SiteContent::save('home', $validated['home']);
        SiteContent::save('shop', $validated['shop']);

        return back()->with('success', 'Le contenu du site a été enregistré.');
    }

    private function rules(): array
    {
        // Un lien doit être relatif (/shop), une ancre (#faq), ou commencer par http(s):, mailto: ou tel:
        // (refuse notamment « javascript: »).
        $link   = ['nullable', 'string', 'max:255', 'regex:/^(\/|#|https?:\/\/|mailto:|tel:)/i'];
        $url    = ['nullable', 'string', 'max:255', 'regex:/^https?:\/\//i'];
        $title  = ['nullable', 'string', 'max:150'];
        $text   = ['nullable', 'string', 'max:600'];
        $icon   = ['nullable', 'string', 'max:16'];
        $flag   = ['required', 'boolean'];
        $count  = ['required', 'integer', 'between:1,12'];

        return [
            'home'                => ['required', 'array'],
            'shop'                => ['required', 'array'],

            /* ----- Accueil ----- */
            'home.hero.title'           => $title,
            'home.hero.subtitle'        => $text,
            'home.hero.primary_label'   => ['nullable', 'string', 'max:60'],
            'home.hero.primary_link'    => $link,
            'home.hero.secondary_label' => ['nullable', 'string', 'max:60'],
            'home.hero.secondary_link'  => $link,

            'home.intro.enabled'        => $flag,
            'home.intro.title'          => $title,
            'home.intro.subtitle'       => $text,
            'home.intro.items'          => ['array', 'max:8'],
            'home.intro.items.*.icon'   => $icon,
            'home.intro.items.*.title'  => ['required', 'string', 'max:80'],
            'home.intro.items.*.text'   => $text,
            'home.intro.items.*.link'   => $link,

            'home.accessories.enabled'       => $flag,
            'home.accessories.title'         => $title,
            'home.accessories.subtitle'      => $text,
            'home.accessories.items'         => ['array', 'max:16'],
            'home.accessories.items.*.icon'  => $icon,
            'home.accessories.items.*.label' => ['required', 'string', 'max:50'],
            'home.accessories.items.*.link'  => $link,

            'home.products.new_enabled'      => $flag,
            'home.products.new_title'        => $title,
            'home.products.new_subtitle'     => $text,
            'home.products.new_count'        => $count,
            'home.products.promo_enabled'    => $flag,
            'home.products.promo_title'      => $title,
            'home.products.promo_subtitle'   => $text,
            'home.products.promo_count'      => $count,
            'home.products.popular_enabled'  => $flag,
            'home.products.popular_title'    => $title,
            'home.products.popular_subtitle' => $text,
            'home.products.popular_count'    => $count,

            'home.banner.enabled'       => $flag,
            'home.banner.title'         => $title,
            'home.banner.text'          => $text,
            'home.banner.button_label'  => ['nullable', 'string', 'max:60'],
            'home.banner.button_link'   => $link,

            'home.why.enabled'          => $flag,
            'home.why.title'            => $title,
            'home.why.subtitle'         => $text,
            'home.why.items'            => ['array', 'max:8'],
            'home.why.items.*.icon'     => $icon,
            'home.why.items.*.title'    => ['required', 'string', 'max:80'],
            'home.why.items.*.text'     => $text,

            'home.reviews.enabled'      => $flag,
            'home.reviews.title'        => $title,
            'home.reviews.items'        => ['array', 'max:8'],
            'home.reviews.items.*.rating' => ['required', 'integer', 'between:1,5'],
            'home.reviews.items.*.text'   => ['required', 'string', 'max:600'],
            'home.reviews.items.*.author' => ['required', 'string', 'max:80'],

            'home.faq.enabled'          => $flag,
            'home.faq.title'            => $title,
            'home.faq.items'            => ['array', 'max:15'],
            'home.faq.items.*.q'        => ['required', 'string', 'max:200'],
            'home.faq.items.*.a'        => ['required', 'string', 'max:1000'],

            'home.newsletter.enabled'      => $flag,
            'home.newsletter.title'        => $title,
            'home.newsletter.text'         => $text,
            'home.newsletter.button_label' => ['nullable', 'string', 'max:40'],

            /* ----- Coordonnées + pied de page ----- */
            'shop.brand'     => ['required', 'string', 'max:60'],
            'shop.tagline'   => ['nullable', 'string', 'max:150'],
            'shop.address'   => ['required', 'string', 'max:190'],
            'shop.phone'     => ['required', 'string', 'max:30'],
            'shop.whatsapp'  => ['required', 'string', 'max:30'],
            'shop.email'     => ['required', 'email', 'max:190'],
            'shop.hours'     => ['nullable', 'string', 'max:120'],
            'shop.facebook'  => $url,
            'shop.instagram' => $url,
            'shop.map_lat'   => ['required', 'numeric', 'between:-90,90'],
            'shop.map_lng'   => ['required', 'numeric', 'between:-180,180'],

            'shop.footer_columns'                   => ['array', 'max:4'],
            'shop.footer_columns.*.title'           => ['required', 'string', 'max:60'],
            'shop.footer_columns.*.links'           => ['array', 'max:10'],
            'shop.footer_columns.*.links.*.label'   => ['required', 'string', 'max:60'],
            'shop.footer_columns.*.links.*.url'     => array_merge(['required'], array_slice($link, 1)),
        ];
    }

    private function messages(): array
    {
        return [
            'regex'    => 'Format invalide : utilisez un lien comme /shop, #faq, https://… , mailto:… ou tel:…',
            'required' => 'Ce champ est obligatoire.',
            'max.string' => 'Texte trop long.',
            'max.array'  => "Trop d'éléments dans cette liste.",
        ];
    }
}
