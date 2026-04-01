@php
    $plant = $plant ?? null;
    $existingImages = $plant && $plant->relationLoaded('images') ? $plant->images : collect();
@endphp

<div class="card-admin p-4 mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-2 mb-3">
        <div>
            <h5 class="mb-1">Imágenes</h5>
            <div class="text-muted small">Carga JPG o PNG de hasta 5 MB por imagen.</div>
        </div>
    </div>

    <input
        type="file"
        name="images[]"
        class="form-control"
        multiple
        accept="image/jpeg,image/png"
        data-preview-target="plantImagesPreview"
    >

    <div id="plantImagesPreview" class="file-preview-grid mt-3"></div>

    @if($existingImages->count())
        <div class="mt-4">
            <h6 class="mb-3">Imágenes actuales</h6>
            <div class="file-preview-grid">
                @foreach($existingImages as $image)
                    <div class="file-preview-item">
                        <img src="{{ asset('storage/' . $image->file_path) }}" alt="{{ $image->file_name }}">
                        <div class="meta d-flex justify-content-between align-items-center gap-2">
                            <span class="text-truncate">{{ $image->file_name }}</span>
                            @if($image->is_primary)
                                <span class="badge bg-berry badge-soft">Principal</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
