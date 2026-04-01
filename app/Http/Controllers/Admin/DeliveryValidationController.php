<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryValidationRequest;
use App\Models\Delivery;
use App\Models\DeliveryValidation;
use App\Services\DeliveryValidationService;
use Illuminate\Http\Request;

class DeliveryValidationController extends Controller
{
    protected $validations;

    public function __construct(DeliveryValidationService $validations)
    {
        $this->validations = $validations;
        $this->middleware('permission:validations.view')->only(['index', 'show']);
        $this->middleware('permission:validations.create')->only(['create', 'store']);
        $this->middleware('permission:validations.update')->only(['edit', 'update']);
        $this->middleware('permission:validations.delete')->only(['destroy']);
        $this->middleware('permission:validations.approve')->only(['approve']);
    }

    public function index(Request $request)
    {
        $records = $this->validations->paginate($request->get('search'));

        return view('admin.shared.index', [
            'title' => 'Validación de entregas',
            'subtitle' => 'Revisión de entregas recibidas',
            'createRoute' => route('admin.delivery-validations.create'),
            'search' => $request->get('search'),
            'records' => $records,
            'columns' => $this->indexColumns(),
            'resource' => 'admin.delivery-validations',
            'actions' => ['show', 'edit', 'delete'],
        ]);
    }

    public function create()
    {
        return view('admin.shared.form', [
            'title' => 'Nueva validación',
            'subtitle' => 'Registra lo recibido en la dirección',
            'route' => route('admin.delivery-validations.store'),
            'method' => 'POST',
            'backRoute' => route('admin.delivery-validations.index'),
            'submitLabel' => 'Guardar validación',
            'entity' => new DeliveryValidation(),
            'sections' => $this->formSections(),
        ]);
    }

    public function store(DeliveryValidationRequest $request)
    {
        $delivery = Delivery::findOrFail($request->input('delivery_id'));
        $this->validations->save($delivery, $request->validated());

        return redirect()->route('admin.delivery-validations.index')->with('success', 'Validación registrada correctamente.');
    }

    public function show(DeliveryValidation $deliveryValidation)
    {
        $validation = $this->validations->find($deliveryValidation->id);

        return view('admin.shared.show', [
            'title' => 'Detalle de validación',
            'subtitle' => 'Consulta la validación de la entrega',
            'backRoute' => route('admin.delivery-validations.index'),
            'editRoute' => route('admin.delivery-validations.edit', $validation),
            'approveRoute' => $validation->status !== 'approved' ? route('admin.delivery-validations.approve', $validation) : null,
            'entity' => $validation,
            'sections' => $this->detailSections(),
        ]);
    }

    public function edit(DeliveryValidation $deliveryValidation)
    {
        $validation = $this->validations->find($deliveryValidation->id);

        return view('admin.shared.form', [
            'title' => 'Editar validación',
            'subtitle' => 'Actualiza la información de la validación',
            'route' => route('admin.delivery-validations.update', $validation),
            'method' => 'PUT',
            'backRoute' => route('admin.delivery-validations.index'),
            'submitLabel' => 'Actualizar validación',
            'entity' => $validation,
            'sections' => $this->formSections($validation),
        ]);
    }

    public function update(DeliveryValidationRequest $request, DeliveryValidation $deliveryValidation)
    {
        $validation = $this->validations->save($deliveryValidation->delivery, $request->validated());

        return redirect()->route('admin.delivery-validations.index')->with('success', 'Validación actualizada correctamente.');
    }

    public function destroy(DeliveryValidation $deliveryValidation)
    {
        $this->validations->delete($deliveryValidation);

        return redirect()->route('admin.delivery-validations.index')->with('success', 'Validación eliminada correctamente.');
    }

    public function approve(DeliveryValidation $deliveryValidation)
    {
        $this->validations->approve($deliveryValidation);

        return redirect()->route('admin.delivery-validations.index')->with('success', 'Validación aprobada correctamente.');
    }

    protected function indexColumns()
    {
        return [
            ['label' => 'Entrega', 'field' => 'delivery.id', 'type' => 'text'],
            ['label' => 'CCT', 'field' => 'delivery.cct.CLAVECCT', 'type' => 'text'],
            ['label' => 'Recibidos', 'field' => 'received_quantity', 'type' => 'text'],
            ['label' => 'Estado', 'field' => 'status', 'type' => 'badge', 'map' => [
                'pending' => ['label' => 'Pendiente', 'class' => 'warning'],
                'validated' => ['label' => 'Validada', 'class' => 'primary'],
                'approved' => ['label' => 'Aprobada', 'class' => 'success'],
                'rejected' => ['label' => 'Rechazada', 'class' => 'danger'],
            ]],
            ['label' => 'Validada', 'field' => 'validated_at', 'type' => 'datetime'],
        ];
    }

    protected function formSections($validation = null)
    {
        return [
            [
                'title' => 'Datos de validación',
                'fields' => [
                    ['name' => 'delivery_id', 'label' => 'Entrega', 'type' => 'select', 'options' => $this->deliveryOptions($validation), 'col' => 6],
                    ['name' => 'received_quantity', 'label' => 'Cantidad recibida', 'type' => 'number', 'col' => 3],
                    ['name' => 'status', 'label' => 'Estado', 'type' => 'select', 'options' => [
                        'pending' => 'Pendiente',
                        'validated' => 'Validada',
                        'approved' => 'Aprobada',
                        'rejected' => 'Rechazada',
                    ], 'col' => 3],
                    ['name' => 'observations', 'label' => 'Observaciones', 'type' => 'textarea', 'rows' => 4, 'col' => 12],
                ],
            ],
        ];
    }

    protected function detailSections()
    {
        return [
            [
                'title' => 'Resumen',
                'fields' => [
                    ['name' => 'delivery.id', 'label' => 'Entrega', 'type' => 'text', 'col' => 3],
                    ['name' => 'delivery.cct.CLAVECCT', 'label' => 'CCT', 'type' => 'text', 'col' => 3],
                    ['name' => 'received_quantity', 'label' => 'Recibidos', 'type' => 'text', 'col' => 3],
                    ['name' => 'status', 'label' => 'Estado', 'type' => 'badge', 'map' => [
                        'pending' => ['label' => 'Pendiente', 'class' => 'warning'],
                        'validated' => ['label' => 'Validada', 'class' => 'primary'],
                        'approved' => ['label' => 'Aprobada', 'class' => 'success'],
                        'rejected' => ['label' => 'Rechazada', 'class' => 'danger'],
                    ], 'col' => 3],
                    ['name' => 'validated_at', 'label' => 'Validada el', 'type' => 'datetime', 'col' => 3],
                    ['name' => 'observations', 'label' => 'Observaciones', 'type' => 'text', 'col' => 12],
                ],
            ],
        ];
    }

    protected function deliveryOptions($validation = null)
    {
        $query = Delivery::with(['cct', 'direction'])->orderBy('delivery_date', 'desc');

        if (!$validation) {
            $query->doesntHave('validation');
        }

        $options = [];

        foreach ($query->get() as $delivery) {
            $options[$delivery->id] = '#' . $delivery->id . ' - ' . optional($delivery->cct)->CLAVECCT . ' / ' . optional($delivery->direction)->name;
        }

        return $options;
    }
}
