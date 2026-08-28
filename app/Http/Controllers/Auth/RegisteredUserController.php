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
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Password::defaults(),
                ],
            ],
            [
                'name.required' => 'Le nom est obligatoire.',
                'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',

                'email.required' => 'L’adresse email est obligatoire.',
                'email.email' => 'L’adresse email n’est pas valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',

                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            ]
        );

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

        return redirect()->route('shop.products');
    }
}