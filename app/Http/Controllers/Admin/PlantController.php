<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlantRequest;
use App\Models\Plant;
use App\Services\PlantService;
use Illuminate\Http\Request;

class PlantController extends Controller
{
    protected $plants;

    public function __construct(PlantService $plants)
    {
        $this->plants = $plants;
        $this->middleware('permission:plants.view')->only(['index', 'show']);
        $this->middleware('permission:plants.create')->only(['store']);
        $this->middleware('permission:plants.update')->only(['update']);
        $this->middleware('permission:plants.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            $search = trim((string) $request->get('search'));

            $plants = Plant::with(['images'])
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere('short_description', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->get()
                ->map(function ($plant) {
                    $primaryImage = $plant->images->firstWhere('is_primary', true);

                    return [
                        'id' => $plant->id,
                        'name' => $plant->name,
                        'slug' => $plant->slug,
                        'short_description' => $plant->short_description,
                        'description_html' => $plant->description_html,
                        'care_instructions' => $plant->care_instructions,
                        'is_active' => (bool) $plant->is_active,
                        'images_count' => $plant->images->count(),
                      'primary_image_url' => $primaryImage ? asset('storage/' . $primaryImage->file_path) : null,
                     'images' => $plant->images->map(function ($image) {
    return [
        'id' => $image->id,
        'url' => asset('storage/' . $image->file_path),
        'path' => $image->file_path,
        'is_primary' => (bool) $image->is_primary,
        'sort_order' => $image->sort_order,
    ];
})->values(),
                    ];
                })
                ->values();

            return response()->json([
                'plants' => $plants,
            ]);
        }

        return view('admin.plants.index', [
            'title' => 'Plantas',
            'subtitle' => 'Catálogo de especies y material vegetal',
        ]);
    }

    public function store(PlantRequest $request)
    {
        $plant = $this->plants->create(
            $request->validated(),
            $request->file('images', []),
            $request->input('primary_image_index')
        );

        $plant->load('images');
        $primaryImage = $plant->images->firstWhere('is_primary', true);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Planta creada correctamente.',
                'plant' => [
                    'id' => $plant->id,
                    'name' => $plant->name,
                    'slug' => $plant->slug,
                    'short_description' => $plant->short_description,
                    'description_html' => $plant->description_html,
                    'care_instructions' => $plant->care_instructions,
                    'is_active' => (bool) $plant->is_active,
                    'images_count' => $plant->images->count(),
                    'primary_image_url' => $primaryImage ? asset('storage/' . $primaryImage->file_path) : null,
                    'images' => $plant->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => asset('storage/' . $image->file_path),
                            'path' => $image->file_path,
                            'is_primary' => (bool) $image->is_primary,
                            'sort_order' => $image->sort_order,
                        ];
                    })->values(),
                ]
            ], 201);
        }

        return redirect()->route('admin.plants.index')->with('success', 'Planta creada correctamente.');
    }

    public function show(Request $request, Plant $plant)
    {
        $plant->load('images');
        $primaryImage = $plant->images->firstWhere('is_primary', true);

        $data = [
            'id' => $plant->id,
            'name' => $plant->name,
            'slug' => $plant->slug,
            'short_description' => $plant->short_description,
            'description_html' => $plant->description_html,
            'care_instructions' => $plant->care_instructions,
            'is_active' => (bool) $plant->is_active,
            'images_count' => $plant->images->count(),
            'primary_image_url' => $primaryImage ? asset('storage/' . $primaryImage->file_path) : null,
            'images' => $plant->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->file_path),
                    'path' => $image->file_path,
                    'is_primary' => (bool) $image->is_primary,
                    'sort_order' => $image->sort_order,
                ];
            })->values(),
        ];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'plant' => $data
            ]);
        }

        return redirect()->route('admin.plants.index');
    }

    public function update(PlantRequest $request, Plant $plant)
    {
       $this->plants->update(
            $plant,
            $request->validated(),
            $request->file('images', []),
            $request->input('primary_image_index'),
            $request->input('removed_images', [])
        );
        $plant->load('images');
        $primaryImage = $plant->images->firstWhere('is_primary', true);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Planta actualizada correctamente.',
                'plant' => [
                    'id' => $plant->id,
                    'name' => $plant->name,
                    'slug' => $plant->slug,
                    'short_description' => $plant->short_description,
                    'description_html' => $plant->description_html,
                    'care_instructions' => $plant->care_instructions,
                    'is_active' => (bool) $plant->is_active,
                    'images_count' => $plant->images->count(),
                    'primary_image_url' => $primaryImage ? asset('storage/' . $primaryImage->file_path) : null,
                    'images' => $plant->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'url' => asset('storage/' . $image->file_path),
                            'path' => $image->file_path,
                            'is_primary' => (bool) $image->is_primary,
                            'sort_order' => $image->sort_order,
                        ];
                    })->values(),
                ]
            ]);
        }

        return redirect()->route('admin.plants.index')->with('success', 'Planta actualizada correctamente.');
    }

    public function destroy(Request $request, Plant $plant)
    {
        $this->plants->delete($plant);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Planta eliminada correctamente.'
            ]);
        }

        return redirect()->route('admin.plants.index')->with('success', 'Planta eliminada correctamente.');
    }
}