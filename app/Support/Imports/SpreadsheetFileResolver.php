<?php

namespace App\Support\Imports;

use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Excel as ExcelReader;

class SpreadsheetFileResolver
{
    private const ALLOWED_MIME_TYPES = [
        'xls' => [
            'application/vnd.ms-excel',
            'application/vnd.ms-office',
            'application/x-msexcel',
            'application/x-ms-excel',
            'application/x-excel',
            'application/excel',
            'application/x-ole-storage',
            'application/cdfv2',
            'application/cdfv2-corrupt',
            'application/x-cdfv2',
            'application/octet-stream',
        ],
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/octet-stream',
        ],
        'csv' => [
            'text/csv',
            'text/plain',
            'application/csv',
            'application/x-csv',
            'text/x-csv',
            'text/comma-separated-values',
            'application/vnd.ms-excel',
        ],
    ];

    private const HTML_MIME_TYPES = [
        'text/html',
        'application/xhtml+xml',
    ];

    public static function validationError(UploadedFile $file): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (! array_key_exists($extension, self::ALLOWED_MIME_TYPES)) {
            return 'Format file tidak didukung. Gunakan file .xls, .xlsx, atau .csv.';
        }

        $mimeType = strtolower((string) $file->getMimeType());

        if ($extension === 'xls' && self::looksLikeHtmlSpreadsheet($file)) {
            return null;
        }

        if (in_array($mimeType, self::ALLOWED_MIME_TYPES[$extension], true)) {
            return null;
        }

        if ($extension === 'xls' && in_array($mimeType, self::HTML_MIME_TYPES, true)) {
            return 'File .xls ini terdeteksi sebagai HTML, tetapi tidak berisi tabel spreadsheet yang valid.';
        }

        return 'Jenis file tidak sesuai. Gunakan file Excel .xls/.xlsx atau CSV yang valid.';
    }

    public static function resolveReaderType(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'xls' => self::looksLikeHtmlSpreadsheet($file) ? self::htmlReaderType() : ExcelReader::XLS,
            'xlsx' => ExcelReader::XLSX,
            'csv' => ExcelReader::CSV,
            default => throw new \InvalidArgumentException('Format file tidak didukung. Gunakan file .xls, .xlsx, atau .csv.'),
        };
    }

    public static function looksLikeHtmlSpreadsheet(UploadedFile $file): bool
    {
        $path = $file->getRealPath();

        if (! $path || ! is_readable($path)) {
            return false;
        }

        $handle = fopen($path, 'rb');

        if (! $handle) {
            return false;
        }

        $sample = strtolower((string) fread($handle, 8192));
        fclose($handle);

        return str_contains($sample, '<table')
            && (str_contains($sample, '<tr') || str_contains($sample, '<td') || str_contains($sample, '<th'));
    }

    private static function htmlReaderType(): string
    {
        return defined(ExcelReader::class.'::HTML')
            ? constant(ExcelReader::class.'::HTML')
            : 'Html';
    }
}
