<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Inscription à la newsletter (publique, avec throttle).
     * Même message que l'adresse soit nouvelle ou déjà inscrite : on ne révèle rien sur les inscrits.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ], [
            'email.required' => 'Veuillez saisir votre adresse e-mail.',
            'email.email'    => 'Cette adresse e-mail ne semble pas valide.',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => mb_strtolower(trim($data['email']))]);

        // Une personne désinscrite qui se réinscrit est réactivée.
        if ($subscriber->unsubscribed_at) {
            $subscriber->update(['unsubscribed_at' => null]);
        }

        return back()->with('success', 'Merci ! Vous êtes bien inscrit(e) à notre newsletter.');
    }
}
