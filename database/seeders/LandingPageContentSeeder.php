<?php

namespace Database\Seeders;

use App\Models\LandingPageContent;
use App\Services\LandingPageContentService;
use Illuminate\Database\Seeder;

class LandingPageContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach (LandingPageContentService::MANAGED_SECTIONS as $sectionKey => $meta) {
            $content = LandingPageContent::firstOrNew(['section_key' => $sectionKey]);

            foreach (($meta['defaults'] ?? []) as $field => $value) {
                if (! $content->exists || $content->{$field} === null || $content->{$field} === '') {
                    $content->{$field} = $value;
                }
            }

            if (! $content->exists) {
                $content->is_active = true;
            }

            $content->save();
        }
    }
}
