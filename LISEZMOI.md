# Promotions + Contact — mise en place

Les fichiers de ce dossier suivent l'arborescence de votre projet : copiez-les à la racine
de `computer_shop/` (ils écrasent les fichiers modifiés, créent les nouveaux).

## 1. Commandes à lancer

```bash
php artisan migrate          # ajoute products.promo_price + tables contact_messages / contact_message_replies
npm run dev                  # (ou npm run build)
```

## 2. Variables .env (facultatif, sinon valeurs par défaut de config/shop.php)

```
CONTACT_PHONE="+229 XX XX XX XX"
CONTACT_WHATSAPP="+229 XX XX XX XX"
CONTACT_EMAIL="contact@votre-domaine.com"
CONTACT_ADDRESS="Cotonou, Bénin"
CONTACT_HOURS="Lun – Sam : 8h – 19h"
```

`MAIL_MAILER=log` : tant que ce n'est pas un vrai SMTP, les réponses de l'admin sont écrites
dans `storage/logs/laravel.log` au lieu d'être envoyées.

## 3. Fichiers

**Nouveaux** : migrations (2), `config/shop.php`, modèles `ContactMessage` / `ContactMessageReply`,
`Shop/ContactController`, `Admin/ContactMessageController`, `Mail/ContactReplyMail` + vue e-mail,
pages `Shop/Promotions.vue`, `Shop/Contact.vue`, `Admin/ContactMessages/Index.vue` et `Show.vue`,
composant `ContactStatusBadge.vue`.

**Modifiés** : `Product.php` (accesseurs `current_price`, `is_on_promotion`, `discount_percent`, scope `onPromotion`),
`Shop/ProductController` (méthode `promotions`), `CartController` et `CheckoutController` (facturation au prix promo),
`Admin/ProductController` (validation `promo_price`), `HandleInertiaRequests` (compteur de nouveaux messages),
`routes/web.php`, `Navbar.vue`, `ProductCard.vue`, `AdminLayout.vue`, formulaires/liste produits admin,
fiche produit, panier, checkout, détail de commande client.

## 4. À tester

1. Admin > Produits > modifier un produit > renseigner « Prix promotionnel » (inférieur au prix) > la page **Promotions** l'affiche avec prix barré et pourcentage.
2. Ajouter ce produit au panier > le total et la commande utilisent bien le prix promo.
3. Page **Contact** en visiteur : envoi du formulaire > message visible dans Admin > **Messages**.
4. Connecté avec une commande : bloc « Vous avez un problème avec une commande ? », ou bouton depuis Mes commandes > détail.
5. Admin : filtrer par statut, rechercher par nom / email / n° de commande, répondre, changer le statut.

## 5. Points à connaître

- Les 4 réponses de la FAQ (dans `Contact.vue`, constante `faqs`) sont volontairement génériques : adaptez-les à vos vraies conditions.
- Une promotion n'a pas de date de fin : pour l'arrêter, videz le prix promo. (Une colonne `promo_ends_at` est facile à ajouter si besoin.)
- Les filtres de prix du catalogue restent basés sur le prix normal (`price`).
- Les pièces jointes sont stockées sur le disque privé `local` (votre `.env` a `FILESYSTEM_DISK=public`) et téléchargeables uniquement depuis le dashboard.
- Anti-spam : 5 messages / 10 minutes par visiteur (`throttle:5,10` dans `routes/web.php`).
