<?php

namespace App\Services;

use App\Models\Plant;
use App\Repositories\PlantImageRepository;
use App\Repositories\PlantRepository;
use Illuminate\Support\Str;

class PlantService
{
    protected $plants;

    protected $plantImages;

    protected $activityLogs;

    public function __construct(PlantRepository $plants, PlantImageRepository $plantImages, ActivityLogService $activityLogs)
    {
        $this->plants = $plants;
        $this->plantImages = $plantImages;
        $this->activityLogs = $activityLogs;
    }

    public function paginate($search = null)
    {
        return $this->plants->paginate(15, $search, ['images'], ['name', 'asc'], ['images']);
    }

    public function find($id)
    {
        return $this->plants->findOrFail($id, ['images']);
    }

    public function create(array $data, array $images = [], $primaryImageIndex = null)
    {
        $data = $this->normalize($data);
        $plant = $this->plants->create($data);

        $this->storeImages($plant, $images, $primaryImageIndex);

        $this->activityLogs->log('plants', 'create', 'Se creó la planta ' . $plant->name, $plant->id, null, $plant->toArray());

        return $plant->load('images');
    }

public function update(Plant $plant, array $data, array $images = [], $primaryImageIndex = null, array $removedImageIds = [])
    {
        $oldValues = $plant->toArray();
        $data = $this->normalize($data, $plant->id);
        $plant = $this->plants->update($plant, $data);
        $this->removeImages($plant, $removedImageIds);
        $this->storeImages($plant, $images, $primaryImageIndex);

        $this->activityLogs->log('plants', 'update', 'Se actualizó la planta ' . $plant->name, $plant->id, $oldValues, $plant->toArray());

        return $plant->load('images');
    }

    public function delete(Plant $plant)
    {
        $oldValues = $plant->toArray();
        $this->plants->delete($plant);

        $this->activityLogs->log('plants', 'delete', 'Se eliminó la planta ' . $oldValues['name'], $oldValues['id'], $oldValues, null);

        return true;
    }

    protected function removeImages(Plant $plant, array $removedImageIds = [])
{
    $removedImageIds = array_filter(array_map('intval', $removedImageIds));

    if (empty($removedImageIds)) {
        return;
    }

    $imagesToDelete = $plant->images()
        ->whereIn('id', $removedImageIds)
        ->get();

    if ($imagesToDelete->isEmpty()) {
        return;
    }

    $deletedPrimary = false;

    foreach ($imagesToDelete as $image) {
        if ((bool) $image->is_primary) {
            $deletedPrimary = true;
        }

        if (!empty($image->file_path)) {
            \Storage::disk('public')->delete($image->file_path);
        }

        $image->delete();
    }

    if ($deletedPrimary) {
        $firstImage = $plant->images()->orderBy('sort_order')->first();

        if ($firstImage) {
            $plant->images()->update(['is_primary' => false]);
            $firstImage->is_primary = true;
            $firstImage->save();
        }
    }
}

    protected function normalize(array $data, $plantId = null)
    {
        $data['is_active'] = isset($data['is_active']) ? (bool) $data['is_active'] : false;

        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = $this->makeUniqueSlug($data['name'], $plantId);
        } elseif (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        if (!empty($data['description_html'])) {
            $data['description_html'] = $this->sanitizeHtml($data['description_html']);
        }

        return $data;
    }

    protected function makeUniqueSlug($name, $plantId = null)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while ($this->plants->query()
            ->when($plantId, function ($query) use ($plantId) {
                $query->where('id', '<>', $plantId);
            })
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function sanitizeHtml($html)
    {
        $html = trim((string) $html);

        if ($html === '') {
            return null;
        }

        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><h1><h2><h3><h4><h5><h6><span><div>';
        $html = strip_tags($html, $allowedTags);
        $html = preg_replace('/on[a-z]+\s*=\s*"[^"]*"/i', '', $html);
        $html = preg_replace("/on[a-z]+\s*=\s*'[^']*'/i", '', $html);
        $html = preg_replace('/javascript:/i', '', $html);

        return $html;
    }

    protected function storeImages(Plant $plant, array $images = [], $primaryImageIndex = null)
    {
        $images = array_values(array_filter($images));

        if (empty($images)) {
            return;
        }

        $folder = 'plants/' . $plant->id;
        $existingCount = $plant->images()->count();
        $hasPrimary = $plant->images()->where('is_primary', true)->exists();
        $selectedPrimary = $primaryImageIndex !== null;

        if ($selectedPrimary) {
            $plant->images()->update(['is_primary' => false]);
            $hasPrimary = false;
        }

        foreach ($images as $index => $image) {
            $storedPath = $image->storePublicly($folder, 'public');

            $isPrimary = false;

            if ($primaryImageIndex !== null && (int) $primaryImageIndex === $index) {
                $isPrimary = true;
            } elseif (!$hasPrimary && $existingCount === 0 && $index === 0) {
                $isPrimary = true;
            }

            $this->plantImages->create([
                'plant_id' => $plant->id,
                'file_path' => $storedPath,
                'file_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getClientMimeType(),
                'file_size' => $image->getSize(),
                'sort_order' => $existingCount + $index,
                'is_primary' => $isPrimary,
            ]);
        }

        if (!$hasPrimary && !$selectedPrimary) {
            $firstImage = $plant->images()->orderBy('sort_order')->first();

            if ($firstImage) {
                $firstImage->is_primary = true;
                $firstImage->save();
            }
        }
    }
}
