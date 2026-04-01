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
        $this->middleware('permission:plants.create')->only(['create', 'store']);
        $this->middleware('permission:plants.update')->only(['edit', 'update']);
        $this->middleware('permission:plants.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $records = $this->plants->paginate($request->get('search'));

        return view('admin.shared.index', [
            'title' => 'Plantas',
            'subtitle' => 'Catálogo de especies y material vegetal',
            'createRoute' => route('admin.plants.create'),
            'search' => $request->get('search'),
            'records' => $records,
            'columns' => $this->indexColumns(),
            'resource' => 'admin.plants',
            'actions' => ['show', 'edit', 'delete'],
        ]);
    }

    public function create()
    {
        return view('admin.shared.form', [
            'title' => 'Nueva planta',
            'subtitle' => 'Registra una especie o planta',
            'route' => route('admin.plants.store'),
            'method' => 'POST',
            'backRoute' => route('admin.plants.index'),
            'submitLabel' => 'Guardar planta',
            'entity' => new Plant(),
            'sections' => $this->formSections(),
            'extraView' => 'admin.plants.partials.images',
            'extraData' => ['plant' => new Plant()],
        ]);
    }

    public function store(PlantRequest $request)
    {
        $this->plants->create(
            $request->validated(),
            $request->file('images', []),
            $request->input('primary_image_index')
        );

        return redirect()->route('admin.plants.index')->with('success', 'Planta creada correctamente.');
    }

    public function show(Plant $plant)
    {
        $plant->load('images');

        return view('admin.shared.show', [
            'title' => 'Detalle de planta',
            'subtitle' => 'Consulta la ficha de la planta',
            'backRoute' => route('admin.plants.index'),
            'editRoute' => route('admin.plants.edit', $plant),
            'entity' => $plant,
            'sections' => $this->detailSections(),
            'extraView' => 'admin.plants.partials.gallery',
            'extraData' => ['plant' => $plant],
        ]);
    }

    public function edit(Plant $plant)
    {
        $plant->load('images');

        return view('admin.shared.form', [
            'title' => 'Editar planta',
            'subtitle' => 'Actualiza la información de la planta',
            'route' => route('admin.plants.update', $plant),
            'method' => 'PUT',
            'backRoute' => route('admin.plants.index'),
            'submitLabel' => 'Actualizar planta',
            'entity' => $plant,
            'sections' => $this->formSections($plant),
            'extraView' => 'admin.plants.partials.images',
            'extraData' => ['plant' => $plant],
        ]);
    }

    public function update(PlantRequest $request, Plant $plant)
    {
        $this->plants->update(
            $plant,
            $request->validated(),
            $request->file('images', []),
            $request->input('primary_image_index')
        );

        return redirect()->route('admin.plants.index')->with('success', 'Planta actualizada correctamente.');
    }

    public function destroy(Plant $plant)
    {
        $this->plants->delete($plant);

        return redirect()->route('admin.plants.index')->with('success', 'Planta eliminada correctamente.');
    }

    protected function indexColumns()
    {
        return [
            ['label' => 'Nombre', 'field' => 'name', 'type' => 'text'],
            ['label' => 'Slug', 'field' => 'slug', 'type' => 'text'],
            ['label' => 'Imágenes', 'field' => 'images_count', 'type' => 'text'],
            ['label' => 'Activo', 'field' => 'is_active', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']]],
        ];
    }

    protected function formSections()
    {
        return [
            [
                'title' => 'Datos de la planta',
                'fields' => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'col' => 6],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'col' => 6],
                    ['name' => 'short_description', 'label' => 'Descripción corta', 'type' => 'textarea', 'rows' => 3, 'col' => 12],
                    ['name' => 'description_html', 'label' => 'Descripción HTML', 'type' => 'richtext', 'rows' => 8, 'col' => 12],
                    ['name' => 'care_instructions', 'label' => 'Cuidados', 'type' => 'textarea', 'rows' => 4, 'col' => 12],
                    ['name' => 'is_active', 'label' => 'Planta activa', 'type' => 'checkbox', 'col' => 12, 'value' => 1],
                ],
            ],
        ];
    }

    protected function detailSections()
    {
        return [
            [
                'title' => 'Información general',
                'fields' => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'col' => 6],
                    ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'col' => 6],
                    ['name' => 'short_description', 'label' => 'Descripción corta', 'type' => 'text', 'col' => 12],
                    ['name' => 'care_instructions', 'label' => 'Cuidados', 'type' => 'text', 'col' => 12],
                    ['name' => 'is_active', 'label' => 'Activo', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']], 'col' => 6],
                    ['name' => 'created_at', 'label' => 'Creado', 'type' => 'datetime', 'col' => 6],
                ],
            ],
        ];
    }
}
