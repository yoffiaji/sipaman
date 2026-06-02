<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLandingPageRequest;
use App\Models\LandingPageContent;
use App\Services\LandingPageContentService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    use LogsAuditTrail;

    public function __construct(private LandingPageContentService $landingPageContentService)
    {
    }

    public function index(): View
    {
        $contents = $this->landingPageContentService->managedSections();
        $sectionMeta = collect($this->landingPageContentService->managedSectionKeys())
            ->mapWithKeys(fn (string $key) => [$key => $this->landingPageContentService->sectionMeta($key)])
            ->all();

        return view('admin.landing-page.index', compact('contents', 'sectionMeta'));
    }

    public function edit(LandingPageContent $landingPage): View
    {
        $this->landingPageContentService->assertManagedSection($landingPage);

        $sectionMeta = $this->landingPageContentService->sectionMeta($landingPage);

        return view('admin.landing-page.edit', compact('landingPage', 'sectionMeta'));
    }

    public function update(UpdateLandingPageRequest $request, LandingPageContent $landingPage): RedirectResponse
    {
        $before = $landingPage->toArray();
        $updated = $this->landingPageContentService->update($landingPage, $request->contentData(), auth()->id());
        $this->logAudit('update', 'landing_page_contents', $landingPage->id, $before, $updated->toArray());

        return redirect()->route('panel.landing-page.index')->with('success', 'Konten landing page berhasil diperbarui.');
    }
}
