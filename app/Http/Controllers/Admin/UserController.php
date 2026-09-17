<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Liste des utilisateurs
     */
    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => Role::whereIn('name', [
                'customer',
                'manager',
                'admin',
            ])->get(['id', 'name']),
        ]);
    }

    /**
     * Modifier le rôle d'un utilisateur
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => [
                'required',
                'string',
                'exists:roles,name',
            ],
        ]);

        // Empêcher l'admin de modifier son propre rôle
        // if ($user->id === auth()->id()) {
        //     return back()->withErrors([
        //         'role' => 'Vous ne pouvez pas modifier votre propre rôle.',
        //     ]);
        // }

        $user->syncRoles([$validated['role']]);

        return back()->with(
            'success',
            'Le rôle de l’utilisateur a été modifié avec succès.'
        );
    }
}