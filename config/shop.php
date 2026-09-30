<?php

/*
| Coordonnées affichées sur la page Contact.
| Renseignez-les dans le .env (CONTACT_PHONE, CONTACT_WHATSAPP, ...) ou modifiez les valeurs par défaut ci-dessous.
*/
return [
    'contact' => [
        'phone'     => env('CONTACT_PHONE', '+229 01 96 74 05 12'),
        'whatsapp'  => env('CONTACT_WHATSAPP', '+229 01 95 47 92 36'),
        'email'     => env('CONTACT_EMAIL', 'Loginovatech216@gmail.com'),
        'address'   => env('CONTACT_ADDRESS', 'Cotonou, Bénin'),
        'hours'     => env('CONTACT_HOURS', 'Lun – Sam : 8h – 20h'),

        // Position de la carte (Cotonou par défaut)
        'map_lat'   => (float) env('CONTACT_MAP_LAT', 6.3703),
        'map_lng'   => (float) env('CONTACT_MAP_LNG', 2.3912),
    ],
];
