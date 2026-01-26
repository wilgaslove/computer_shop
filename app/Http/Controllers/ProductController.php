<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
// public function share(Request $request): array
// {
//     return array_merge(parent::share($request), [
//         'auth' => [
//             'user' => Auth::user(),
//             'can' => [
//                 'product' => [
//                     'view'   => Auth::user()?->can('product.view') ?? false,
//                     'create' => Auth::user()?->can('product.create') ?? false,
//                     'edit'   => Auth::user()?->can('product.edit') ?? false,
//                     'delete' => Auth::user()?->can('product.delete') ?? false,
//                 ],
//             ],
//         ],
//     ]);
// }
    public function create()
    {
        return Inertia::render('Products/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'stock'       => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index');
    }
}
