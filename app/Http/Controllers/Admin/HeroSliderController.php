<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            ->only(['edit', 'update', 'toggle']);

        $this->middleware('permission:hero-slider.delete')
            ->only(['destroy']);
    }

    /**
     * Liste des bannières
     */
    public function index()
    {
        $sliders = HeroSlider::orderBy('position')
            ->orderBy('id')
            ->get();

        return Inertia::render('Admin/HeroSliders/Index', [
            'sliders' => $sliders,
        ]);
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return Inertia::render('Admin/HeroSliders/Create');
    }

    /**
     * Enregistrer une bannière
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'integer', 'min:0'],
            'active' => ['boolean'],
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
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

    /**
     * Formulaire de modification
     */
    public function edit(HeroSlider $heroSlider)
    {
        return Inertia::render('Admin/HeroSliders/Edit', [
            'slider' => $heroSlider,
        ]);
    }

    /**
     * Modifier une bannière
     */
    public function update(Request $request, HeroSlider $heroSlider)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:255'],
            'position' => ['required', 'integer', 'min:0'],
            'active' => ['boolean'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Nouvelle image
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            // Supprimer l'ancienne image
            if ($heroSlider->image) {
                Storage::disk('public')->delete($heroSlider->image);
            }

            // Enregistrer la nouvelle
            $validated['image'] = $request
                ->file('image')
                ->store('hero-sliders', 'public');
        }

        $heroSlider->update($validated);

        return redirect()
            ->route('admin.hero-sliders.index')
            ->with('success', 'Bannière modifiée avec succès.');
    }

    /**
     * Activer / désactiver une bannière
     */
    public function toggle(HeroSlider $heroSlider)
    {
        $heroSlider->update([
            'active' => ! $heroSlider->active,
        ]);

        return back()->with(
            'success',
            $heroSlider->active
                ? 'Bannière activée.'
                : 'Bannière désactivée.'
        );
    }

    /**
     * Supprimer une bannière
     */
    public function destroy(HeroSlider $heroSlider)
    {
        /*
        |--------------------------------------------------------------------------
        | Supprimer l'image
        |--------------------------------------------------------------------------
        */

        if ($heroSlider->image) {
            Storage::disk('public')->delete($heroSlider->image);
        }

        $heroSlider->delete();

        return redirect()
            ->route('admin.hero-sliders.index')
            ->with('success', 'Bannière supprimée avec succès.');
    }

    /**
     * Affichage d'une bannière
     */
    public function show(HeroSlider $heroSlider)
    {
        return Inertia::render('Admin/HeroSliders/Show', [
            'slider' => $heroSlider,
        ]);
    }
}