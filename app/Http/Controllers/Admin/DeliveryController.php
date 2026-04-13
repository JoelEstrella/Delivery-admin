<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryRequest;
use App\Models\Cct;
use App\Models\Delivery;
use App\Models\Direction;
use App\Models\Plant;
use App\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    protected $deliveries;

    public function __construct(DeliveryService $deliveries)
    {
        $this->deliveries = $deliveries;
        $this->middleware('permission:deliveries.view')->only(['index', 'show']);
        $this->middleware('permission:deliveries.create')->only(['create', 'store']);
        $this->middleware('permission:deliveries.update')->only(['edit', 'update']);
        $this->middleware('permission:deliveries.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $records = $this->deliveries->paginate($request->get('search'));

        return view('admin.deliveries.index', [
            'title' => 'Entregas',
            'subtitle' => 'Registro de entrega de plantas a CCT',
            'createRoute' => route('admin.deliveries.create'),
            'search' => $request->get('search'),
            'records' => $records,
            'columns' => $this->indexColumns(),
            'resource' => 'admin.deliveries',
            'actions' => ['show', 'edit', 'delete'],
        ]);
    }

    public function create()
    {
        return view('admin.shared.form', [
            'title' => 'Nueva entrega',
            'subtitle' => 'Captura una entrega de plantas',
            'route' => route('admin.deliveries.store'),
            'method' => 'POST',
            'backRoute' => route('admin.deliveries.index'),
            'submitLabel' => 'Guardar entrega',
            'entity' => new Delivery(),
            'sections' => $this->formSections(),
            'extraView' => 'admin.deliveries.partials.items',
            'extraData' => [
                'deliveryItems' => [],
                'plants' => Plant::orderBy('name')->get(),
            ],
        ]);
    }

    public function store(DeliveryRequest $request)
    {
        $payload = $request->validated();
        $items = $payload['items'];
        unset($payload['items']);

        $this->deliveries->create($payload, $items);

        return redirect()->route('admin.deliveries.index')->with('success', 'Entrega creada correctamente.');
    }

    public function show(Delivery $delivery)
    {
        $delivery = $this->deliveries->find($delivery->id);

        return view('admin.shared.show', [
            'title' => 'Detalle de entrega',
            'subtitle' => 'Consulta la entrega y sus plantas',
            'backRoute' => route('admin.deliveries.index'),
            'editRoute' => route('admin.deliveries.edit', $delivery),
            'entity' => $delivery,
            'sections' => $this->detailSections(),
            'extraView' => 'admin.deliveries.partials.show',
            'extraData' => ['delivery' => $delivery],
        ]);
    }

    public function edit(Delivery $delivery)
    {
        $delivery = $this->deliveries->find($delivery->id);

        return view('admin.shared.form', [
            'title' => 'Editar entrega',
            'subtitle' => 'Actualiza la entrega de plantas',
            'route' => route('admin.deliveries.update', $delivery),
            'method' => 'PUT',
            'backRoute' => route('admin.deliveries.index'),
            'submitLabel' => 'Actualizar entrega',
            'entity' => $delivery,
            'sections' => $this->formSections($delivery),
            'extraView' => 'admin.deliveries.partials.items',
            'extraData' => [
                'deliveryItems' => $delivery->items->map(function ($item) {
                    return [
                        'plant_id' => $item->plant_id,
                        'quantity' => $item->quantity,
                    ];
                })->values()->toArray(),
                'plants' => Plant::orderBy('name')->get(),
            ],
        ]);
    }

    public function update(DeliveryRequest $request, Delivery $delivery)
    {
        $payload = $request->validated();
        $items = $payload['items'];
        unset($payload['items']);

        $this->deliveries->update($delivery, $payload, $items);

        return redirect()->route('admin.deliveries.index')->with('success', 'Entrega actualizada correctamente.');
    }

    public function destroy(Delivery $delivery)
    {
        $this->deliveries->delete($delivery);

        return redirect()->route('admin.deliveries.index')->with('success', 'Entrega eliminada correctamente.');
    }

    protected function indexColumns()
    {
        return [
            ['label' => 'ID', 'field' => 'id', 'type' => 'text'],
            ['label' => 'CCT', 'field' => 'cct.CLAVECCT', 'type' => 'text'],
            ['label' => 'Dirección', 'field' => 'direction.name', 'type' => 'text'],
            ['label' => 'Fecha', 'field' => 'delivery_date', 'type' => 'date'],
            ['label' => 'Estado', 'field' => 'status', 'type' => 'badge', 'map' => [
                'pending' => ['label' => 'Pendiente', 'class' => 'warning'],
                'delivered' => ['label' => 'Entregada', 'class' => 'success'],
                'validated' => ['label' => 'Validada', 'class' => 'primary'],
                'approved' => ['label' => 'Aprobada', 'class' => 'success'],
                'cancelled' => ['label' => 'Cancelada', 'class' => 'secondary'],
            ]],
        ];
    }

    protected function formSections($delivery = null)
    {
        return [
            [
                'title' => 'Datos de la entrega',
                'fields' => [
                    ['name' => 'cct_id', 'label' => 'CCT', 'type' => 'select', 'options' => $this->cctOptions(), 'col' => 6],
                    ['name' => 'direction_id', 'label' => 'Dirección', 'type' => 'select', 'options' => $this->directionOptions(), 'col' => 6],
                    ['name' => 'delivery_date', 'label' => 'Fecha de entrega', 'type' => 'date', 'col' => 4],
                    ['name' => 'status', 'label' => 'Estado', 'type' => 'select', 'options' => [
                        'pending' => 'Pendiente',
                        'delivered' => 'Entregada',
                        'cancelled' => 'Cancelada',
                    ], 'col' => 4],
                    ['name' => 'delivered_by', 'label' => 'Entregado por', 'type' => 'text', 'col' => 4],
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
                    ['name' => 'id', 'label' => 'ID', 'type' => 'text', 'col' => 3],
                    ['name' => 'cct.CLAVECCT', 'label' => 'CCT', 'type' => 'text', 'col' => 3],
                    ['name' => 'direction.name', 'label' => 'Dirección', 'type' => 'text', 'col' => 3],
                    ['name' => 'delivery_date', 'label' => 'Fecha', 'type' => 'date', 'col' => 3],
                    ['name' => 'status', 'label' => 'Estado', 'type' => 'badge', 'map' => [
                        'pending' => ['label' => 'Pendiente', 'class' => 'warning'],
                        'delivered' => ['label' => 'Entregada', 'class' => 'success'],
                        'validated' => ['label' => 'Validada', 'class' => 'primary'],
                        'approved' => ['label' => 'Aprobada', 'class' => 'success'],
                        'cancelled' => ['label' => 'Cancelada', 'class' => 'secondary'],
                    ], 'col' => 3],
                    ['name' => 'delivered_by', 'label' => 'Entregado por', 'type' => 'text', 'col' => 3],
                    ['name' => 'observations', 'label' => 'Observaciones', 'type' => 'text', 'col' => 12],
                ],
            ],
        ];
    }

    protected function cctOptions()
    {
        $options = [];

        foreach (Cct::orderBy('NOMBRECT')->get() as $cct) {
            $options[$cct->id] = $cct->CLAVECCT . ' - ' . $cct->NOMBRECT;
        }

        return $options;
    }

    protected function directionOptions()
    {
        $options = [];

        foreach (Direction::orderBy('name')->get() as $direction) {
            $options[$direction->id] = $direction->name;
        }

        return $options;
    }
}
