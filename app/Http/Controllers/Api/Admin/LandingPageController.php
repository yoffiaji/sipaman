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
        $contents = LandingPageContent::active()
            ->orderBy('section_key')
            ->get()
            ->keyBy('section_key');

        return response()->json(['data' => $contents]);
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => LandingPageContent::with('updatedBy:id,nama')->orderBy('section_key')->get(),
        ]);
    }

    public function update(UpdateLandingPageRequest $request, string $section): JsonResponse
    {
        $content = LandingPageContent::where('section_key', $section)->firstOrFail();
        $before = $content->toArray();
        $updated = $this->landingPageContentService->update($content, $request->validated(), auth()->id());

        $this->logAudit('update', 'landing_page_contents', $content->id, $before, $updated->toArray());

        return response()->json([
            'message' => "Section '{$section}' berhasil diperbarui.",
            'data' => $updated->load('updatedBy:id,nama'),
        ]);
    }
}
