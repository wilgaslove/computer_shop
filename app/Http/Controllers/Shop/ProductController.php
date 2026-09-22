<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ProductController extends Controller
{

    public function index()
    {
        $products = Product::where('active', true)
            ->latest()
            ->paginate(12);

        $heroSliders = HeroSlider::where('active', true)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return Inertia::render('Shop/Products/Index', [
            'products' => $products,
            'heroSliders' => $heroSliders,
        ]);
    }

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

    public function show(Product $product)
    {
        abort_if(! $product->active, 404);

        $product->load('category');

        return Inertia::render('Shop/Products/Show', [
            'product' => $product,
        ]);
    }
}
