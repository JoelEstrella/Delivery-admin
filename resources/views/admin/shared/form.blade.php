@extends('layouts.admin')

@section('title', $title . ' | ' . config('app.name', 'Sistema administrativo'))

@section('content')
    @php
        $entity = $entity ?? null;
        $sections = $sections ?? [];
        $extraView = $extraView ?? null;
        $extraData = $extraData ?? [];
        $method = strtoupper($method ?? 'POST');
    @endphp

    <div class="card-admin p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <div class="page-title">{{ $title }}</div>
                @if(!empty($subtitle))
                    <div class="page-subtitle">{{ $subtitle }}</div>
                @endif
            </div>
            <div>
                <a href="{{ $backRoute ?? url()->previous() }}" class="btn btn-outline-secondary">Volver</a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <div class="fw-semibold mb-2">Revisa los siguientes errores:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        @foreach($sections as $section)
            <div class="card-admin p-4 mb-4">
                <div class="mb-3">
                    <h5 class="mb-1">{{ $section['title'] ?? '' }}</h5>
                    @if(!empty($section['description']))
                        <div class="text-muted small">{{ $section['description'] }}</div>
                    @endif
                </div>

                <div class="row g-3">
                    @foreach($section['fields'] as $field)
                        @php
                            $fieldName = $field['name'];
                            $fieldType = $field['type'] ?? 'text';
                            $fieldValue = old($fieldName, data_get($entity, $fieldName, $field['value'] ?? null));
                            $col = $field['col'] ?? 6;
                            $options = $field['options'] ?? [];
                            $help = $field['help'] ?? null;
                        @endphp

                        @if($fieldType === 'hidden')
                            <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
                            @continue
                        @endif

                        <div class="col-12 col-md-{{ $col }}">
                            <label class="form-label fw-semibold">{{ $field['label'] ?? $fieldName }}</label>

                            @if($fieldType === 'select')
                                <select name="{{ $fieldName }}" class="form-select">
                                    <option value="">Seleccione...</option>
                                    @foreach($options as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" {{ (string) $fieldValue === (string) $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif($fieldType === 'textarea' || $fieldType === 'richtext')
                                <textarea
                                    name="{{ $fieldName }}"
                                    rows="{{ $field['rows'] ?? 4 }}"
                                    class="form-control"
                                    @if($fieldType === 'richtext') data-richtext="true" @endif
                                >{{ $fieldValue }}</textarea>
                            @elseif($fieldType === 'checkbox')
                                <input type="hidden" name="{{ $fieldName }}" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="{{ $fieldName }}" name="{{ $fieldName }}" value="1" {{ (string) $fieldValue === '1' || $fieldValue === 1 || $fieldValue === true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $fieldName }}">{{ $field['description'] ?? 'Activo' }}</label>
                                </div>
                            @elseif($fieldType === 'checkbox_group')
                                @php
                                    $selected = old($fieldName, $field['selected'] ?? []);
                                    if (!is_array($selected)) {
                                        $selected = [$selected];
                                    }
                                @endphp
                                <div class="row g-2">
                                    @foreach($options as $optionValue => $optionLabel)
                                        @php $checked = in_array((string) $optionValue, array_map('strval', $selected), true); @endphp
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label class="border rounded-3 p-2 w-100 d-flex align-items-start gap-2 bg-white">
                                                <input type="checkbox" class="form-check-input mt-1" name="{{ $fieldName }}[]" value="{{ $optionValue }}" {{ $checked ? 'checked' : '' }}>
                                                <span>{{ $optionLabel }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($fieldType === 'file')
                                <input
                                    type="file"
                                    name="{{ $fieldName }}{{ !empty($field['multiple']) ? '[]' : '' }}"
                                    class="form-control"
                                    @if(!empty($field['multiple'])) multiple @endif
                                    @if(!empty($field['accept'])) accept="{{ $field['accept'] }}" @endif
                                    @if(!empty($field['previewTarget'])) data-preview-target="{{ $field['previewTarget'] }}" @endif
                                >
                            @else
                                <input
                                    type="{{ $fieldType }}"
                                    name="{{ $fieldName }}"
                                    value="{{ $fieldType === 'password' ? '' : $fieldValue }}"
                                    class="form-control"
                                    @if(!empty($field['step'])) step="{{ $field['step'] }}" @endif
                                    @if(!empty($field['min'])) min="{{ $field['min'] }}" @endif
                                    @if(!empty($field['max'])) max="{{ $field['max'] }}" @endif
                                    @if($fieldType === 'password') autocomplete="new-password" @endif
                                >
                            @endif

                            @error($fieldName)
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            @if($help)
                                <div class="text-muted small mt-1">{{ $help }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if(!empty($extraView))
            @include($extraView, $extraData)
        @endif

        <div class="card-admin p-4 d-flex justify-content-between align-items-center">
            <a href="{{ $backRoute ?? url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary px-4">{{ $submitLabel ?? 'Guardar' }}</button>
        </div>
    </form>
@endsection
