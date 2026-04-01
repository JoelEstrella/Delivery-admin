@extends('layouts.admin')

@section('title', $title . ' | ' . config('app.name', 'Sistema administrativo'))

@section('content')
    @php
        $entity = $entity ?? null;
        $sections = $sections ?? [];
        $extraView = $extraView ?? null;
        $extraData = $extraData ?? [];
    @endphp

    <div class="card-admin ui-card p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <div class="ui-page-title">{{ $title }}</div>
                @if(!empty($subtitle))
                    <div class="ui-page-subtitle">{{ $subtitle }}</div>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if(!empty($backRoute))
                    <a href="{{ $backRoute }}" class="ui-btn ui-btn--secondary">Volver</a>
                @endif
                @if(!empty($editRoute))
                    <a href="{{ $editRoute }}" class="ui-btn ui-btn--primary">Editar</a>
                @endif
                @if(!empty($approveRoute))
                    <form action="{{ $approveRoute }}" method="POST" class="d-inline" data-confirm="true" data-confirm-message="¿Deseas aprobar esta validación?">
                        @csrf
                        <button type="submit" class="ui-btn ui-btn--positive">Aprobar</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @foreach($sections as $section)
        <div class="card-admin ui-card p-4 mb-4">
            <h5 class="mb-3">{{ $section['title'] ?? '' }}</h5>

            <div class="row g-3">
                @foreach($section['fields'] as $field)
                    @php
                        $fieldName = $field['name'];
                        $fieldType = $field['type'] ?? 'text';
                        $fieldValue = data_get($entity, $fieldName, $field['value'] ?? null);
                        $col = $field['col'] ?? 6;
                    @endphp

                    <div class="col-12 col-md-{{ $col }}">
                        <div class="border rounded-3 p-3 h-100 bg-white surface-alt">
                            <div class="text-muted-soft small text-uppercase mb-1">{{ $field['label'] ?? $fieldName }}</div>

                            @if($fieldType === 'badge')
                                @php
                                    $map = $field['map'] ?? [];
                                    $badge = array_key_exists($fieldValue, $map) ? $map[$fieldValue] : ['label' => $fieldValue, 'class' => 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $badge['class'] }} badge-soft ui-badge ui-badge--soft">{{ $badge['label'] }}</span>
                            @elseif($fieldType === 'boolean')
                                <span class="badge bg-{{ $fieldValue ? 'success' : 'secondary' }} badge-soft ui-badge ui-badge--soft">{{ $fieldValue ? 'Sí' : 'No' }}</span>
                            @elseif($fieldType === 'date')
                                <div class="fw-semibold">{{ $fieldValue ? \Illuminate\Support\Carbon::parse($fieldValue)->format('d/m/Y') : '—' }}</div>
                            @elseif($fieldType === 'datetime')
                                <div class="fw-semibold">{{ $fieldValue ? \Illuminate\Support\Carbon::parse($fieldValue)->format('d/m/Y H:i') : '—' }}</div>
                            @elseif($fieldType === 'html')
                                <div>{!! $fieldValue !!}</div>
                            @else
                                <div class="fw-semibold">{{ $fieldValue !== null && $fieldValue !== '' ? $fieldValue : '—' }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if(!empty($extraView))
        @include($extraView, $extraData)
    @endif
@endsection
