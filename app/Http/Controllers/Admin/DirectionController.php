<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DirectionRequest;
use App\Models\Direction;
use App\Services\DirectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
        $records = Direction::all();

        if ($request->expectsJson()) {
            return Response::success(
                'Registros obtenidos correctamente.',
                $records
            );
        }

        return view('admin.organizations.index', [
            'title' => 'Direcciones',
            'subtitle' => 'Catálogo de Direcciones y Departamentos'
        ]);
    }

    public function create()
    {
        return Response::success(
            'Registros obtenidos correctamente.',
            [
                'title' => 'Direcciones',
                'subtitle' => 'Crear Dirección',
                'method' => 'POST',
                'submitLabel' => 'Guardar Dirección'
            ]
        );
    }

    public function store(DirectionRequest $request)
    {
        $direction = $this->directions->create($request->validated());

        if ($request->expectsJson()) {
            return Response::success(
                'Registro creado correctamente.',
                $direction,
                201
            );
        }
    }

    public function show(Direction $direction)
    {
        $direction->load('stocks.plant');

        return Response::success(
            'Registros obtenidos correctamente.',
            $direction
        );
    }

    public function edit(Direction $direction)
    {
        return Response::success(
            'Registros obtenidos correctamente.',
            [
                'title' => 'Direcciones',
                'subtitle' => 'Editar Dirección',
                'method' => 'PUT',
                'submitLabel' => 'Actualizar Dirección',
                'direction' => $direction
            ]
        );
    }

    public function update(DirectionRequest $request, Direction $direction)
    {

        $directionUpdate = $this->directions->update($direction, $request->validated());

        return Response::success(
            'Registros actualizados correctamente.',
            $directionUpdate
        );
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
