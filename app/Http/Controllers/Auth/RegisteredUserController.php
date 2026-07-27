<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Affiche la page d'inscription.
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
            ]);

            // Attribution du rôle Spatie
            $user->assignRole('customer');

            DB::commit();

            Auth::login($user);

            return redirect()->route('shop.products');

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors([
                'register' => $e->getMessage(),
            ]);
        }
    }
}