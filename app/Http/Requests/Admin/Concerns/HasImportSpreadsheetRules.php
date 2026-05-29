<?php

namespace App\Http\Requests\Admin\Concerns;

use App\Rules\ImportSpreadsheetFile;

trait HasImportSpreadsheetRules
{
    protected function importSpreadsheetRules(): array
    {
        return [
            'required',
            'file',
            'max:10240',
            'extensions:xls,xlsx,csv',
            new ImportSpreadsheetFile(),
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File import wajib dipilih.',
            'file.file' => 'Upload harus berupa file.',
            'file.max' => 'Ukuran file import maksimal 10 MB.',
            'file.extensions' => 'Format file tidak didukung. Gunakan file .xls, .xlsx, atau .csv.',
        ];
    }
}
