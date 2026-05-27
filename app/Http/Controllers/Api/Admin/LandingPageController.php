<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPageContent;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * LandingPageController
 * ---------------------
 * GET  /api/landing-page              → Publik, baca semua section
 * GET  /api/admin/landing             → Admin: daftar section untuk edit
 * PUT  /api/admin/landing/{section}   → Admin: update konten section
 */
class LandingPageController extends Controller
{
    use LogsAuditTrail;

    // Publik — dipakai frontend untuk render landing page
    public function publicIndex(): JsonResponse
    {
        $contents = LandingPageContent::all()->keyBy('section_key');
        return response()->json(['data' => $contents]);
    }

    // Admin — daftar semua section beserta siapa yang terakhir edit
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => LandingPageContent::with('updatedBy:id,nama')->get(),
        ]);
    }

    // Admin — update konten section tertentu
    public function update(Request $request, string $section): JsonResponse
    {
        $data = $request->validate([
            'judul'  => 'nullable|string|max:200',
            'konten' => 'nullable|string',
        ]);

        $content = LandingPageContent::firstOrNew(['section_key' => $section]);
        $sebelum = $content->exists ? $content->toArray() : null;

        $content->fill([
            'judul'      => $data['judul']  ?? $content->judul,
            'konten'     => $data['konten'] ?? $content->konten,
            'updated_by' => auth()->id(),
        ]);
        $content->save();

        $this->logAudit('update', 'landing_page_contents', $content->id, $sebelum, $content->toArray());

        return response()->json([
            'message' => "Section '{$section}' berhasil diperbarui.",
            'data'    => $content->fresh()->load('updatedBy:id,nama'),
        ]);
    }
}
