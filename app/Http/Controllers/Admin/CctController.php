<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CctRequest;
use App\Models\Cct;
use App\Services\CctService;
use Illuminate\Http\Request;

class CctController extends Controller
{
    protected $ccts;

    public function __construct(CctService $ccts)
    {
        $this->ccts = $ccts;
        $this->middleware('permission:ccts.view')->only(['index', 'show']);
        $this->middleware('permission:ccts.create')->only(['create', 'store']);
        $this->middleware('permission:ccts.update')->only(['edit', 'update']);
        $this->middleware('permission:ccts.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $records = $this->ccts->paginate($request->get('search'));

        return view('admin.shared.index', [
            'title' => 'CCT',
            'subtitle' => 'Gestión de centros de trabajo',
            'createRoute' => route('admin.ccts.create'),
            'search' => $request->get('search'),
            'records' => $records,
            'columns' => $this->indexColumns(),
            'resource' => 'admin.ccts',
            'actions' => ['show', 'edit', 'delete'],
        ]);
    }

    public function create()
    {
        return view('admin.shared.form', [
            'title' => 'Nuevo CCT',
            'subtitle' => 'Captura la información del centro escolar',
            'route' => route('admin.ccts.store'),
            'method' => 'POST',
            'backRoute' => route('admin.ccts.index'),
            'submitLabel' => 'Guardar CCT',
            'entity' => new Cct(),
            'sections' => $this->formSections(),
        ]);
    }

    public function store(CctRequest $request)
    {
        $this->ccts->create($request->validated());

        return redirect()->route('admin.ccts.index')->with('success', 'CCT creado correctamente.');
    }

    public function show(Cct $cct)
    {
        return view('admin.shared.show', [
            'title' => 'Detalle de CCT',
            'subtitle' => 'Consulta la ficha del centro escolar',
            'backRoute' => route('admin.ccts.index'),
            'editRoute' => route('admin.ccts.edit', $cct),
            'entity' => $cct,
            'sections' => $this->detailSections(),
        ]);
    }

    public function edit(Cct $cct)
    {
        return view('admin.shared.form', [
            'title' => 'Editar CCT',
            'subtitle' => 'Actualiza la información del centro escolar',
            'route' => route('admin.ccts.update', $cct),
            'method' => 'PUT',
            'backRoute' => route('admin.ccts.index'),
            'submitLabel' => 'Actualizar CCT',
            'entity' => $cct,
            'sections' => $this->formSections(),
        ]);
    }

    public function update(CctRequest $request, Cct $cct)
    {
        $this->ccts->update($cct, $request->validated());

        return redirect()->route('admin.ccts.index')->with('success', 'CCT actualizado correctamente.');
    }

    public function destroy(Cct $cct)
    {
        $this->ccts->delete($cct);

        return redirect()->route('admin.ccts.index')->with('success', 'CCT eliminado correctamente.');
    }

    protected function indexColumns()
    {
        return [
            ['label' => 'Clave', 'field' => 'CLAVECCT', 'type' => 'text'],
            ['label' => 'Nombre', 'field' => 'NOMBRECT', 'type' => 'text'],
            ['label' => 'Localidad', 'field' => 'N_LOCALIDAD', 'type' => 'text'],
            ['label' => 'Municipio', 'field' => 'N_MUNICIPIO', 'type' => 'text'],
            ['label' => 'Director', 'field' => 'DIRECTOR', 'type' => 'text'],
            ['label' => 'Estado', 'field' => 'STATUS', 'type' => 'badge', 'map' => [1 => ['label' => 'Activo', 'class' => 'success'], 0 => ['label' => 'Inactivo', 'class' => 'secondary']]],
        ];
    }

    protected function formSections()
    {
        return [
            [
                'title' => 'Datos generales',
                'fields' => [
                    ['name' => 'CLAVECCT', 'label' => 'CLAVECCT', 'type' => 'text', 'col' => 4],
                    ['name' => 'NOMBRECT', 'label' => 'Nombre del centro', 'type' => 'text', 'col' => 8],
                    ['name' => 'TURNO', 'label' => 'Turno', 'type' => 'number', 'col' => 3],
                    ['name' => 'NIVEL', 'label' => 'Nivel', 'type' => 'number', 'col' => 3],
                    ['name' => 'N_NIVEL', 'label' => 'Nombre del nivel', 'type' => 'text', 'col' => 6],
                    ['name' => 'TIPO', 'label' => 'Tipo', 'type' => 'text', 'col' => 6],
                    ['name' => 'STATUS', 'label' => 'Estado', 'type' => 'number', 'col' => 6, 'value' => 1],
                    ['name' => 'ZONAESCOLA', 'label' => 'Zona escolar', 'type' => 'text', 'col' => 6],
                    ['name' => 'CCT_ZONA', 'label' => 'CCT zona', 'type' => 'text', 'col' => 6],
                    ['name' => 'LOCALIDAD', 'label' => 'Localidad', 'type' => 'text', 'col' => 3],
                    ['name' => 'N_LOCALIDAD', 'label' => 'Nombre localidad', 'type' => 'text', 'col' => 9],
                    ['name' => 'MUNICIPIO', 'label' => 'Municipio', 'type' => 'text', 'col' => 3],
                    ['name' => 'N_MUNICIPIO', 'label' => 'Nombre municipio', 'type' => 'text', 'col' => 9],
                    ['name' => 'REGIONT', 'label' => 'Región técnica', 'type' => 'text', 'col' => 6],
                    ['name' => 'REGIONOP', 'label' => 'Región operativa', 'type' => 'text', 'col' => 6],
                    ['name' => 'CCT_SERREG', 'label' => 'Serie región', 'type' => 'text', 'col' => 6],
                    ['name' => 'CCT_INMUEBLE', 'label' => 'CCT inmueble', 'type' => 'text', 'col' => 6],
                ],
            ],
            [
                'title' => 'Ubicación',
                'fields' => [
                    ['name' => 'DOMICILIO', 'label' => 'Domicilio', 'type' => 'text', 'col' => 12],
                    ['name' => 'ENTRECALLE', 'label' => 'Entre calle', 'type' => 'text', 'col' => 4],
                    ['name' => 'YCALLE', 'label' => 'Y calle', 'type' => 'text', 'col' => 4],
                    ['name' => 'CALLEPOST', 'label' => 'Calle postal', 'type' => 'text', 'col' => 4],
                    ['name' => 'NUMEXT', 'label' => 'Número exterior', 'type' => 'text', 'col' => 3],
                    ['name' => 'ALFANUMEXT', 'label' => 'Alfanum exterior', 'type' => 'text', 'col' => 3],
                    ['name' => 'NUMINT', 'label' => 'Número interior', 'type' => 'text', 'col' => 3],
                    ['name' => 'ALFANUMINT', 'label' => 'Alfanum interior', 'type' => 'text', 'col' => 3],
                    ['name' => 'COLONIA', 'label' => 'Colonia', 'type' => 'text', 'col' => 4],
                    ['name' => 'ASENTAMIEN', 'label' => 'Asentamiento', 'type' => 'text', 'col' => 4],
                    ['name' => 'CODPOST', 'label' => 'Código postal', 'type' => 'text', 'col' => 4],
                    ['name' => 'DES_UBIC', 'label' => 'Descripción ubicación', 'type' => 'textarea', 'rows' => 4, 'col' => 12],
                    ['name' => 'LONGITUD', 'label' => 'Longitud', 'type' => 'number', 'step' => '0.000001', 'col' => 6],
                    ['name' => 'LATITUD', 'label' => 'Latitud', 'type' => 'number', 'step' => '0.000001', 'col' => 6],
                ],
            ],
            [
                'title' => 'Contacto y fechas',
                'fields' => [
                    ['name' => 'DIRECTOR', 'label' => 'Director', 'type' => 'text', 'col' => 4],
                    ['name' => 'APELLIDO1', 'label' => 'Apellido 1', 'type' => 'text', 'col' => 4],
                    ['name' => 'APELLIDO2', 'label' => 'Apellido 2', 'type' => 'text', 'col' => 4],
                    ['name' => 'CURP', 'label' => 'CURP', 'type' => 'text', 'col' => 4],
                    ['name' => 'RFC', 'label' => 'RFC', 'type' => 'text', 'col' => 4],
                    ['name' => 'TELEFONO', 'label' => 'Teléfono', 'type' => 'text', 'col' => 4],
                    ['name' => 'TELEXTEN', 'label' => 'Tel. ext.', 'type' => 'text', 'col' => 4],
                    ['name' => 'CELULAR1', 'label' => 'Celular', 'type' => 'text', 'col' => 4],
                    ['name' => 'CORREOELE', 'label' => 'Correo electrónico', 'type' => 'email', 'col' => 6],
                    ['name' => 'PAGINAWEB', 'label' => 'Página web', 'type' => 'text', 'col' => 6],
                    ['name' => 'SOSTENIMIE', 'label' => 'Sostenimiento', 'type' => 'text', 'col' => 6],
                    ['name' => 'SERVICIO', 'label' => 'Servicio', 'type' => 'textarea', 'rows' => 4, 'col' => 12],
                    ['name' => 'FECHAFUNDA', 'label' => 'Fecha funda', 'type' => 'text', 'col' => 3],
                    ['name' => 'FECHAALTA', 'label' => 'Fecha alta', 'type' => 'date', 'col' => 3],
                    ['name' => 'FECHACLAUS', 'label' => 'Fecha clausura', 'type' => 'date', 'col' => 3],
                    ['name' => 'FECHAACTUA', 'label' => 'Fecha actual', 'type' => 'date', 'col' => 3],
                ],
            ],
        ];
    }

    protected function detailSections()
    {
        return $this->formSections();
    }
}
