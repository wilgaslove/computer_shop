<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HeroSliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hero-slider.view')
            ->only(['index', 'show']);

        $this->middleware('permission:hero-slider.create')
            ->only(['create', 'store']);

        $this->middleware('permission:hero-slider.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:hero-slider.delete')
            ->only(['destroy']);
    }

    public function index()
    {
        $sliders = HeroSlider::orderBy('position')->get();

        return Inertia::render('Admin/HeroSliders/Index', [
            'sliders' => $sliders,
        ]);
    }


    public function create()
    {
        return Inertia::render('Admin/HeroSliders/Create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:255',
            'position' => 'required|integer',
            'active' => 'boolean',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {

            $validated['image'] = $request
                ->file('image')
                ->store('hero-sliders', 'public');
        }

        HeroSlider::create($validated);

        return redirect()
            ->route('admin.hero-sliders.index')
            ->with('success', 'Bannière créée avec succès.');
    }


    public function show(HeroSlider $heroSlider)
    {
        //
    }


    public function edit(HeroSlider $heroSlider)
    {
        return Inertia::render('Admin/HeroSliders/Edit', [
            'slider' => $heroSlider,
        ]);
    }


    public function update(Request $request, HeroSlider $heroSlider)
    {
        //
    }

    public function destroy(HeroSlider $heroSlider)
    {
        //
    }
}
