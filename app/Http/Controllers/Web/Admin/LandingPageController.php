<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLandingPageRequest;
use App\Models\LandingPageContent;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    use LogsAuditTrail;

    public function index(): View
    {
        $contents = LandingPageContent::with('updatedBy')->orderBy('section_key')->get();

        return view('admin.landing-page.index', compact('contents'));
    }

    public function update(UpdateLandingPageRequest $request, LandingPageContent $landingPage): RedirectResponse
    {
        $before = $landingPage->toArray();
        $landingPage->update(array_merge($request->validated(), ['updated_by' => auth()->id()]));
        $this->logAudit('update', 'landing_page_contents', $landingPage->id, $before, $landingPage->fresh()->toArray());

        return back()->with('success', 'Konten landing page berhasil diperbarui.');
    }
}
