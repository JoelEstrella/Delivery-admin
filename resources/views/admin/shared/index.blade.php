@extends('layouts.admin')

@section('title', $title . ' | ' . config('app.name', 'Sistema administrativo'))

@section('content')
    @php
        $records = $records ?? collect();
        $columns = $columns ?? [];
        $resource = $resource ?? null;
        $actions = $actions ?? [];
        $createRoute = $createRoute ?? null;
    @endphp

    <div class="card-admin p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <div class="page-title">{{ $title }}</div>
                @if(!empty($subtitle))
                    <div class="page-subtitle">{{ $subtitle }}</div>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($createRoute)
                    <a href="{{ $createRoute }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                        <i data-feather="plus" width="16" height="16"></i>
                        Nuevo registro
                    </a>
                @endif
            </div>
        </div>

        <form method="GET" class="mt-4">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-8 col-lg-6">
                    <label class="form-label small text-muted">Búsqueda</label>
                    <input type="text" name="search" value="{{ $search ?? request('search') }}" class="form-control" placeholder="Buscar...">
                </div>
                <div class="col-12 col-md-auto">
                    <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                    <a href="{{ url()->current() }}" class="btn btn-link text-decoration-none">Limpiar</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-admin p-3 p-md-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-admin datatable" data-server-pagination="true">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        @if($resource && !empty($actions))
                            <th class="text-end">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            @foreach($columns as $column)
                                @php
                                    $type = $column['type'] ?? 'text';
                                    $field = $column['field'] ?? null;
                                    $value = $field ? data_get($record, $field) : null;
                                @endphp
                                <td>
                                    @if($type === 'badge')
                                        @php
                                            $map = $column['map'] ?? [];
                                            $badge = array_key_exists($value, $map) ? $map[$value] : ['label' => $value, 'class' => 'secondary'];
                                        @endphp
                                        <span class="badge bg-{{ $badge['class'] }} badge-soft">{{ $badge['label'] }}</span>
                                    @elseif($type === 'boolean')
                                        <span class="badge bg-{{ $value ? 'success' : 'secondary' }} badge-soft">{{ $value ? 'Sí' : 'No' }}</span>
                                    @elseif($type === 'date')
                                        {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y') : '—' }}
                                    @elseif($type === 'datetime')
                                        {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('d/m/Y H:i') : '—' }}
                                    @elseif($type === 'html')
                                        {!! $value !!}
                                    @else
                                        {{ $value !== null && $value !== '' ? $value : '—' }}
                                    @endif
                                </td>
                            @endforeach

                            @if($resource && !empty($actions))
                                <td class="text-end actions">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if(in_array('show', $actions))
                                            <a href="{{ route($resource . '.show', $record) }}" class="btn btn-outline-primary">Ver</a>
                                        @endif
                                        @if(in_array('edit', $actions))
                                            <a href="{{ route($resource . '.edit', $record) }}" class="btn btn-outline-secondary">Editar</a>
                                        @endif
                                        @if(in_array('delete', $actions))
                                            <form action="{{ route($resource . '.destroy', $record) }}" method="POST" class="d-inline" data-confirm="true" data-confirm-message="¿Deseas eliminar este registro?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + (($resource && !empty($actions)) ? 1 : 0) }}" class="text-center text-muted py-5">
                                No hay registros para mostrar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $records->links() }}
        </div>
    </div>
@endsection
