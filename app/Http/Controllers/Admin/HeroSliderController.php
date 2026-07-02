<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HeroSliderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hero-slider.view')
            ->only(['index','show']);

        $this->middleware('permission:hero-slider.create')
            ->only(['create','store']);

        $this->middleware('permission:hero-slider.edit')
            ->only(['edit','update']);

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
        //
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