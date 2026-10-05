<?php

/*
| Référencement (SEO).
| Modifiez ces valeurs ici ou via le .env. La marque et le slogan viennent de
| l'admin (Contenu du site) ; l'adresse, le téléphone et les horaires de config/shop.php.
*/
return [
    // Ville et pays utilisés dans les titres et descriptions (recherches locales : « informatique Cotonou »)
    'locality'     => env('SEO_LOCALITY', 'Cotonou'),
    'country_name' => env('SEO_COUNTRY_NAME', 'Bénin'),
    'country'      => env('SEO_COUNTRY_CODE', 'BJ'),

    // Devise des prix (FCFA = XOF)
    'currency'     => 'XOF',

    // Description de la page d'accueil (140 à 160 caractères idéalement)
    'description'  => env(
        'SEO_DESCRIPTION',
        'Achetez ordinateurs portables, PC gaming, accessoires et matériel informatique à Cotonou, Bénin. Prix en FCFA, livraison et paiement à la livraison.'
    ),

    // Image affichée lors d'un partage (WhatsApp, Facebook…) : 1200 x 630 px
    'og_image'     => '/images/og-default.jpg',

    // Format schema.org : Mo-Sa 08:00-20:00 (lundi au samedi, 8h à 20h)
    'opening_hours' => ['Mo-Sa 08:00-20:00'],

    'og_locale'    => 'fr_FR',
];
