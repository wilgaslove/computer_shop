<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Contenu éditable du site (page d'accueil, coordonnées, pied de page).
 *
 * - Les valeurs par défaut sont définies ici : le site fonctionne dès l'installation.
 * - Ce que l'admin enregistre (page « Contenu de l'accueil ») est stocké en base et prend le dessus.
 * - Deux groupes : « home » (page d'accueil) et « shop » (coordonnées + pied de page).
 */
class SiteContent
{
    private const CACHE_KEY = 'site_content';

    public static function get(string $group): array
    {
        $stored = self::stored()[$group] ?? [];

        return self::merge(self::defaults()[$group] ?? [], is_array($stored) ? $stored : []);
    }

    public static function save(string $group, array $data): void
    {
        SiteSetting::updateOrCreate(['key' => $group], ['value' => $data]);

        Cache::forget(self::CACHE_KEY);
    }

    /** Valeurs enregistrées en base (mises en cache). */
    private static function stored(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, fn () => SiteSetting::all()
                ->mapWithKeys(fn (SiteSetting $s) => [$s->key => $s->value])
                ->all());
        } catch (Throwable) {
            // Table absente (migrations pas encore lancées) : on retombe sur les valeurs par défaut.
            return [];
        }
    }

    /**
     * Superpose les valeurs enregistrées aux valeurs par défaut.
     * Les groupes de champs sont fusionnés ; les listes (items, liens…) sont remplacées en bloc.
     */
    private static function merge(array $defaults, array $stored): array
    {
        $result = $defaults;

        foreach ($defaults as $key => $default) {
            if (! array_key_exists($key, $stored)) {
                continue;
            }

            $value = $stored[$key];

            $result[$key] = (is_array($default) && ! array_is_list($default) && is_array($value))
                ? self::merge($default, $value)
                : $value;
        }

        return $result;
    }

    public static function defaults(): array
    {
        $contact = config('shop.contact');

        return [

            /* ------------------------------------------------------------------
             | Coordonnées + pied de page (utilisés par le footer, la page Contact, les e-mails)
             ------------------------------------------------------------------ */
            'shop' => [
                'brand'     => 'ComputerShop',
                'tagline'   => 'Votre spécialiste informatique',
                'address'   => $contact['address'],
                'phone'     => $contact['phone'],
                'whatsapp'  => $contact['whatsapp'],
                'email'     => $contact['email'],
                'hours'     => $contact['hours'],
                'facebook'  => null,
                'instagram' => null,
                'map_lat'   => $contact['map_lat'],
                'map_lng'   => $contact['map_lng'],

                'footer_columns' => [
                    [
                        'title' => 'Produits',
                        'links' => [
                            ['label' => 'Ordinateurs', 'url' => '/shop?q=ordinateur'],
                            ['label' => 'PC Gaming', 'url' => '/shop?q=gaming'],
                            ['label' => 'Accessoires', 'url' => '/#accessoires'],
                            ['label' => 'Promotions', 'url' => '/promotions'],
                        ],
                    ],
                    [
                        'title' => 'Assistance',
                        'links' => [
                            ['label' => 'Contact', 'url' => '/contact'],
                            ['label' => 'FAQ', 'url' => '/#faq'],
                            ['label' => 'Livraison', 'url' => '/#faq'],
                            ['label' => 'Retours', 'url' => '/#faq'],
                        ],
                    ],
                    [
                        'title' => 'À propos',
                        'links' => [
                            ['label' => 'Notre boutique', 'url' => '/#ce-que-nous-vendons'],
                            ['label' => 'Nos services', 'url' => '/#pourquoi-nous'],
                        ],
                    ],
                ],
            ],

            /* ------------------------------------------------------------------
             | Page d'accueil
             ------------------------------------------------------------------ */
            'home' => [

                // Texte d'accueil, affiché quand aucune bannière (slider) n'est active
                'hero' => [
                    'title'           => 'Votre spécialiste informatique',
                    'subtitle'        => "Ordinateurs portables, PC Gaming, accessoires et équipements professionnels : trouvez le bon matériel, au bon prix.",
                    'primary_label'   => 'Voir nos produits',
                    'primary_link'    => '/shop',
                    'secondary_label' => 'Nos promotions',
                    'secondary_link'  => '/promotions',
                ],

                // Ce que nous vendons
                'intro' => [
                    'enabled'  => true,
                    'title'    => 'Ce que nous vendons',
                    'subtitle' => "Du PC dont vous avez besoin aux accessoires pour bien l'utiliser.",
                    'items'    => [
                        ['icon' => '💻', 'title' => 'Ordinateurs portables', 'text' => 'Pour les études, le travail et le quotidien.', 'link' => '/shop?q=portable'],
                        ['icon' => '🎮', 'title' => 'PC Gaming', 'text' => 'Des machines puissantes pour jouer sans compromis.', 'link' => '/shop?q=gaming'],
                        ['icon' => '🖥️', 'title' => 'PC de bureau', 'text' => 'Fiabilité et performance pour la maison et le bureau.', 'link' => '/shop?q=bureau'],
                        ['icon' => '🔌', 'title' => 'Accessoires & équipements', 'text' => 'Tout pour compléter et faire évoluer votre installation.', 'link' => '/#accessoires'],
                    ],
                ],

                // Complétez votre setup
                'accessories' => [
                    'enabled'  => true,
                    'title'    => 'Complétez votre setup',
                    'subtitle' => 'Souris, claviers, écrans, stockage… tout pour votre poste de travail.',
                    'items'    => [
                        ['icon' => '🖱️', 'label' => 'Souris', 'link' => '/shop?q=souris'],
                        ['icon' => '⌨️', 'label' => 'Claviers', 'link' => '/shop?q=clavier'],
                        ['icon' => '🎧', 'label' => 'Casques', 'link' => '/shop?q=casque'],
                        ['icon' => '🖥️', 'label' => 'Écrans', 'link' => '/shop?q=écran'],
                        ['icon' => '💾', 'label' => 'SSD', 'link' => '/shop?q=ssd'],
                        ['icon' => '🧠', 'label' => 'RAM', 'link' => '/shop?q=ram'],
                        ['icon' => '🎒', 'label' => 'Sacoches', 'link' => '/shop?q=sacoche'],
                        ['icon' => '🔗', 'label' => 'Adaptateurs', 'link' => '/shop?q=adaptateur'],
                        ['icon' => '📷', 'label' => 'Webcams', 'link' => '/shop?q=webcam'],
                        ['icon' => '🟦', 'label' => 'Tapis de souris', 'link' => '/shop?q=tapis'],
                    ],
                ],

                // Produits à regarder / acheter (alimentés automatiquement par le catalogue)
                'products' => [
                    'new_enabled'     => true,
                    'new_title'       => 'Nouveautés',
                    'new_subtitle'    => 'Les derniers produits ajoutés à la boutique.',
                    'new_count'       => 8,
                    'promo_enabled'   => true,
                    'promo_title'     => 'Promotions du moment',
                    'promo_subtitle'  => 'Des prix réduits, tant que les stocks sont disponibles.',
                    'promo_count'     => 4,
                    'popular_enabled' => true,
                    'popular_title'   => 'Les plus demandés',
                    'popular_subtitle' => 'Les produits que nos clients commandent le plus.',
                    'popular_count'   => 4,
                ],

                // Bannière spéciale
                'banner' => [
                    'enabled'      => true,
                    'title'        => "Besoin d'un ordinateur sur mesure ?",
                    'text'         => 'Contactez notre équipe pour être conseillé.',
                    'button_label' => 'Nous contacter',
                    'button_link'  => '/contact',
                ],

                // Pourquoi acheter chez nous
                'why' => [
                    'enabled'  => true,
                    'title'    => 'Pourquoi acheter chez nous ?',
                    'subtitle' => 'Des engagements simples pour acheter l\'esprit tranquille.',
                    'items'    => [
                        ['icon' => '🚚', 'title' => 'Livraison rapide', 'text' => 'Recevez votre commande dans les meilleurs délais.'],
                        ['icon' => '🔒', 'title' => 'Paiement sécurisé', 'text' => 'Vos paiements sont protégés.'],
                        ['icon' => '🛡️', 'title' => 'Garantie', 'text' => 'Des produits couverts par une garantie.'],
                        ['icon' => '💬', 'title' => 'Support client', 'text' => 'Notre équipe vous accompagne avant et après votre achat.'],
                        ['icon' => '↩️', 'title' => 'Retours', 'text' => 'Un souci avec votre achat ? Contactez-nous, nous trouvons une solution.'],
                    ],
                ],

                // Avis clients : désactivés par défaut. Remplacez l'exemple par de vrais avis avant d'activer.
                'reviews' => [
                    'enabled' => false,
                    'title'   => 'Ils nous font confiance',
                    'items'   => [
                        [
                            'rating' => 5,
                            'text'   => "J'ai acheté mon ordinateur pour mes études. La livraison s'est très bien passée.",
                            'author' => 'Jean A.',
                        ],
                    ],
                ],

                // FAQ (aussi affichée sur la page Contact)
                'faq' => [
                    'enabled' => true,
                    'title'   => 'Questions fréquentes',
                    'items'   => [
                        ['q' => 'Comment passer une commande ?', 'a' => "Ajoutez vos produits au panier, puis validez la commande en renseignant vos informations de livraison. Un compte client permet de suivre ses commandes."],
                        ['q' => 'Quels sont les moyens de paiement ?', 'a' => 'Vous pouvez payer à la livraison ou par Mobile Money.'],
                        ['q' => 'Quels sont les délais de livraison ?', 'a' => 'Ils dépendent de votre ville et de la disponibilité du produit. Le statut « Expédiée » indique que votre commande est en route ; pour un délai précis, contactez-nous.'],
                        ['q' => 'Les ordinateurs sont-ils garantis ?', 'a' => "La durée de garantie dépend du produit et de la marque. Conservez la référence de votre commande comme preuve d'achat et contactez-nous pour toute demande de prise en charge."],
                        ['q' => 'Puis-je retourner un produit ?', 'a' => "Écrivez-nous via la page Contact en indiquant votre numéro de commande et le motif du retour : nous vous indiquons la marche à suivre."],
                        ['q' => 'Comment suivre ma commande ?', 'a' => 'Connectez-vous, puis ouvrez « Mes commandes » : le statut (en attente, confirmée, expédiée, livrée) est mis à jour à chaque étape.'],
                    ],
                ],

                'newsletter' => [
                    'enabled'      => true,
                    'title'        => 'Restez informé de nos nouveautés',
                    'text'         => 'Nouveaux produits, promotions, arrivages et offres spéciales, directement dans votre boîte mail.',
                    'button_label' => "S'inscrire",
                ],
            ],
        ];
    }
}
