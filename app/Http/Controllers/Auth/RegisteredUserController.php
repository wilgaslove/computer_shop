<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Afficher la page d'inscription.
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Enregistrer un nouvel utilisateur.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed'],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // Attribution du rôle par Spatie
    $user->assignRole('customer');

    // Connexion automatique
    Auth::login($user);

    // Régénération de la session
    $request->session()->regenerate();

    // Redirection vers la boutique
    return redirect()->route('shop.products');
}
}