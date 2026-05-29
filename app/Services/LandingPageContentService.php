<?php

namespace App\Services;

use App\Models\LandingPageContent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LandingPageContentService
{
    public function update(LandingPageContent $content, array $data, ?int $userId): LandingPageContent
    {
        $oldImagePath = $content->image_path;
        $newImagePath = null;

        if (($data['image'] ?? null) instanceof UploadedFile) {
            $newImagePath = $data['image']->store('landing-page', 'public');
            $data['image_path'] = $newImagePath;
        } elseif (! empty($data['remove_image'])) {
            $data['image_path'] = null;
        }

        unset($data['image'], $data['remove_image']);

        $content->fill($data);
        $content->updated_by = $userId;
        $content->save();

        if ($oldImagePath && ($newImagePath || array_key_exists('image_path', $data))) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $content->fresh('updatedBy');
    }
}
