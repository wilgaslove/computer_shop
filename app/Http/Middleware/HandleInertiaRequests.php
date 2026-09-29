<?php

namespace App\Http\Middleware;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
                'is_staff' => $request->user()?->hasAnyRole(['admin', 'manager']) ?? false,
                'is_admin' => $request->user()?->hasRole('admin') ?? false,
                'can' => [
                    'product' => [
                        'view'   => Gate::allows('product.view') ?? false,
                        'create' => Gate::allows('product.create') ?? false,
                        'edit'   => Gate::allows('product.edit') ?? false,
                        'delete' => Gate::allows('product.delete') ?? false,
                    ],
                ],
            ],
            // Pastille « nouveaux messages » du menu admin (uniquement pour l'équipe)
            'admin' => [
                'new_messages' => fn () => $request->user()?->hasAnyRole(['admin', 'manager'])
                    ? ContactMessage::where('status', 'new')->count()
                    : 0,
            ],
            'cart' => [
                'count' => array_sum(session('cart', [])),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
        ]);
    }

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }
}
