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
        ]);
    }

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }
}
