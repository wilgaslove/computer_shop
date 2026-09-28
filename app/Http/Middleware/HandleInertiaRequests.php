<?php

namespace App\Http\Middleware;

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
                'can' => [
                    'product' => [
                        'view'   => Gate::allows('product.view') ?? false,
                        'create' => Gate::allows('product.create') ?? false,
                        'edit'   => Gate::allows('product.edit') ?? false,
                        'delete' => Gate::allows('product.delete') ?? false,
                    ],
                ],
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
