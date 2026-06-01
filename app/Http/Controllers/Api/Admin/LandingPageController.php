<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLandingPageRequest;
use App\Models\LandingPageContent;
use App\Services\LandingPageContentService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;

class LandingPageController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private LandingPageContentService $landingPageContentService)
    {
    }

    public function publicIndex(): JsonResponse
    {
        $contents = $this->landingPageContentService->managedSectionsForPublic();

        return response()->json(['data' => $contents]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => $this->landingPageContentService->managedSections(),
            'sections' => collect($this->landingPageContentService->managedSectionKeys())
                ->mapWithKeys(fn (string $key) => [$key => $this->landingPageContentService->sectionMeta($key)]),
        ]);
    }

    public function update(UpdateLandingPageRequest $request, string $section): JsonResponse
    {
        $content = LandingPageContent::where('section_key', $section)->firstOrFail();
        $this->landingPageContentService->assertManagedSection($content);
        $before = $content->toArray();
        $updated = $this->landingPageContentService->update($content, $request->contentData(), auth()->id());

        $this->logAudit('update', 'landing_page_contents', $content->id, $before, $updated->toArray());

        return response()->json([
            'message' => "Section '{$section}' berhasil diperbarui.",
            'data' => $updated->load('updatedBy:id,nama'),
        ]);
    }
}
