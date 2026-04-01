<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DirectionRequest;
use App\Models\Direction;
use App\Services\DirectionService;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    protected $directions;

    public function __construct(DirectionService $directions)
    {
        $this->directions = $directions;
        $this->middleware('permission:directions.view')->only(['index', 'show']);
        $this->middleware('permission:directions.create')->only(['create', 'store']);
        $this->middleware('permission:directions.update')->only(['edit', 'update']);
        $this->middleware('permission:directions.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $records = $this->directions->paginate($request->get('search'));

        return view('admin.shared.index', [
            'title' => 'Direcciones',
            'subtitle' => 'Unidades receptoras de stock',
            'createRoute' => route('admin.directions.create'),
            'search' => $request->get('search'),
            'records' => $records,
            'columns' => $this->indexColumns(),
            'resource' => 'admin.directions',
            'actions' => ['show', 'edit', 'delete'],
        ]);
    }

    public function create()
    {
        return view('admin.shared.form', [
            'title' => 'Nueva dirección',
            'subtitle' => 'Registra una dirección operativa',
            'route' => route('admin.directions.store'),
            'method' => 'POST',
            'backRoute' => route('admin.directions.index'),
            'submitLabel' => 'Guardar dirección',
            'entity' => new Direction(),
            'sections' => $this->formSections(),
        ]);
    }

    public function store(DirectionRequest $request)
    {
        $this->directions->create($request->validated());

        return redirect()->route('admin.directions.index')->with('success', 'Dirección creada correctamente.');
    }

    public function show(Direction $direction)
    {
        $direction->load('stocks.plant');

        return view('admin.shared.show', [
            'title' => 'Detalle de dirección',
            'subtitle' => 'Consulta la ficha de la dirección',
            'backRoute' => route('admin.directions.index'),
            'editRoute' => route('admin.directions.edit', $direction),
            'entity' => $direction,
            'sections' => $this->detailSections(),
            'extraView' => 'admin.directions.partials.stocks',
            'extraData' => ['direction' => $direction],
        ]);
    }

    public function edit(Direction $direction)
    {
        return view('admin.shared.form', [
            'title' => 'Editar dirección',
            'subtitle' => 'Actualiza la dirección operativa',
            'route' => route('admin.directions.update', $direction),
            'method' => 'PUT',
            'backRoute' => route('admin.directions.index'),
            'submitLabel' => 'Actualizar dirección',
            'entity' => $direction,
            'sections' => $this->formSections($direction),
        ]);
    }

    public function update(DirectionRequest $request, Direction $direction)
    {
        $this->directions->update($direction, $request->validated());

        return redirect()->route('admin.directions.index')->with('success', 'Dirección actualizada correctamente.');
    }

    public function destroy(Direction $direction)
    {
        $this->directions->delete($direction);

        return redirect()->route('admin.directions.index')->with('success', 'Dirección eliminada correctamente.');
    }

    protected function indexColumns()
    {
        return [
            ['label' => 'Nombre', 'field' => 'name', 'type' => 'text'],
            ['label' => 'Código', 'field' => 'code', 'type' => 'text'],
            ['label' => 'Responsable', 'field' => 'responsible_name', 'type' => 'text'],
            ['label' => 'Correo', 'field' => 'email', 'type' => 'text'],
            ['label' => 'Activo', 'field' => 'is_active', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']]],
        ];
    }

    protected function formSections()
    {
        return [
            [
                'title' => 'Datos de la dirección',
                'fields' => [
                    ['name' => 'name', 'label' => 'Nombre', 'type' => 'text', 'col' => 6],
                    ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'col' => 6],
                    ['name' => 'responsible_name', 'label' => 'Responsable', 'type' => 'text', 'col' => 6],
                    ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text', 'col' => 6],
                    ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'email', 'col' => 6],
                    ['name' => 'address', 'label' => 'Dirección', 'type' => 'textarea', 'rows' => 4, 'col' => 12],
                    ['name' => 'is_active', 'label' => 'Dirección activa', 'type' => 'checkbox', 'col' => 12, 'value' => 1],
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
                    ['name' => 'code', 'label' => 'Código', 'type' => 'text', 'col' => 6],
                    ['name' => 'responsible_name', 'label' => 'Responsable', 'type' => 'text', 'col' => 6],
                    ['name' => 'phone', 'label' => 'Teléfono', 'type' => 'text', 'col' => 6],
                    ['name' => 'email', 'label' => 'Correo electrónico', 'type' => 'text', 'col' => 6],
                    ['name' => 'is_active', 'label' => 'Activo', 'type' => 'badge', 'map' => [1 => ['label' => 'Sí', 'class' => 'success'], 0 => ['label' => 'No', 'class' => 'secondary']], 'col' => 6],
                    ['name' => 'address', 'label' => 'Dirección', 'type' => 'text', 'col' => 12],
                ],
            ],
        ];
    }
}
