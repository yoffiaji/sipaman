<?php

namespace App\Rules;

use App\Support\Imports\SpreadsheetFileResolver;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ImportSpreadsheetFile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('File import tidak valid.');

            return;
        }

        $error = SpreadsheetFileResolver::validationError($value);

        if ($error !== null) {
            $fail($error);
        }
    }
}
