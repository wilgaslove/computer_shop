<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:product.view')->only(['index', 'show']);
        $this->middleware('permission:product.create')->only(['create', 'store']);
        $this->middleware('permission:product.edit')->only(['edit', 'update']);
        $this->middleware('permission:product.delete')->only(['destroy']);
        $this->middleware('permission:product.edit')->only(['destroyImage']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user(); // ✅ SAFE

        $products = Product::with('category')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'can' => [
                'create' => $user->can('product.create'),
                'edit'   => $user->can('product.edit'),
                'delete' => $user->can('product.delete'),
            ],
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'active'      => 'boolean',
        ]);

        $galleryFiles = $request->file('images', []);
        unset($validated['images']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product = Product::create($validated);

        foreach ($galleryFiles as $position => $file) {
            $path = $file->store('products/gallery', 'public');

            $product->images()->create([
                'path'     => $path,
                'position' => $position,
            ]);
        }

        // Si aucune image de couverture n'a été fournie, on utilise
        // la première image de la galerie comme couverture.
        if (! $product->image && $product->images()->exists()) {
            $product->update([
                'image' => $product->images()->first()->path,
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit créé avec succès');
    }


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return Inertia::render('Admin/Products/Show', [
            'product' => $product->load(['category', 'images']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Product $product)
    // {
    //     return Inertia::render('Admin/Products/Edit', [
    //         'product'    => $product,
    //         'categories' => Category::all(),
    //     ]);
    // }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load(['category', 'images']),
            'categories' => Category::all(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'active'      => 'boolean',
        ]);

        $galleryFiles = $request->file('images', []);
        unset($validated['images']);

        if ($request->hasFile('image')) {
            if ($product->image) {
               Storage::disk('public')->delete($product->image);
 
            }

            $validated['image'] = $request->file('image')
                ->store('products', 'public');
        }

        $product->update($validated);

        if (! empty($galleryFiles)) {
            $nextPosition = (int) $product->images()->max('position') + 1;

            foreach ($galleryFiles as $file) {
                $path = $file->store('products/gallery', 'public');

                $product->images()->create([
                    'path'     => $path,
                    'position' => $nextPosition++,
                ]);
            }
        }

        if (! $product->image && $product->images()->exists()) {
            $product->update([
                'image' => $product->images()->first()->path,
            ]);
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit mis à jour');
    }

    /**
     * Supprimer une image de la galerie d'un produit.
     */
    public function destroyImage(Product $product, \App\Models\ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        // Si l'image de couverture supprimée était celle-ci, on la
        // remplace par la prochaine image disponible de la galerie.
        if ($product->image === $image->path) {
            $next = $product->images()->first();

            $product->update([
                'image' => $next?->path,
            ]);
        }

        return back()->with('success', 'Image supprimée');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->back()
            ->with('success', 'Produit supprimé');
    }
}




