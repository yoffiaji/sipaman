<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\HasImportSpreadsheetRules;
use Illuminate\Foundation\Http\FormRequest;

class ImportCommitmentStatusRequest extends FormRequest
{
    use HasImportSpreadsheetRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => $this->importSpreadsheetRules(),
        ];
    }
}
