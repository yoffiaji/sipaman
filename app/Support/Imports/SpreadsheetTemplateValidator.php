<?php

namespace App\Support\Imports;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReader;

class SpreadsheetTemplateValidator
{
    public function assertRekapPirt(UploadedFile $file, string $readerType): void
    {
        $headers = $this->flattenRows($this->readFirstRows($file, $readerType, 6));

        if ($this->looksLikeCommitmentStatus($headers)) {
            throw new InvalidArgumentException('File ini terlihat seperti file Status Pemenuhan Komitmen. Upload file tersebut di menu Verifikasi.');
        }

        if (! $this->looksLikeRekapPirt($headers)) {
            throw new InvalidArgumentException('Template file Rekap Data PIRT tidak sesuai. Header wajib: No SPPIRT, Nama Branding Produk, Kategori Pangan, Jenis Pangan, NIB, Nama Pelaku Usaha, dan Alamat.');
        }
    }

    public function assertCommitmentStatus(UploadedFile $file, string $readerType): void
    {
        $headers = $this->flattenRows($this->readFirstRows($file, $readerType, 5));

        if ($this->looksLikeRekapPirt($headers)) {
            throw new InvalidArgumentException('File ini terlihat seperti file Rekap Data PIRT. Upload file tersebut di menu Produk.');
        }

        if (! $this->looksLikeCommitmentStatus($headers)) {
            throw new InvalidArgumentException('Template file Status Pemenuhan Komitmen tidak sesuai. Header wajib: No SPPIRT, NIB, Verifikasi Produk, Verifikasi Label, PKP, CPPOB, dan Status Pemenuhan Komitmen.');
        }
    }

    private function readFirstRows(UploadedFile $file, string $readerType, int $maxRows): array
    {
        $path = $file->getRealPath();

        if (! $path || ! is_readable($path)) {
            throw new InvalidArgumentException('File import tidak bisa dibaca. Coba upload ulang file yang valid.');
        }

        $reader = $this->makeReader($readerType);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = min(Coordinate::columnIndexFromString($highestColumn), 30);
        $highestRow = min($sheet->getHighestRow(), $maxRows);
        $rows = [];

        for ($row = 1; $row <= $highestRow; $row++) {
            $values = [];

            for ($column = 1; $column <= $highestColumnIndex; $column++) {
                $cell = Coordinate::stringFromColumnIndex($column).$row;
                $value = $sheet->getCell($cell)->getFormattedValue();
                $values[] = $this->normalizeHeader($value);
            }

            $rows[] = $values;
        }

        $spreadsheet->disconnectWorksheets();

        return $rows;
    }

    private function makeReader(string $readerType): IReader
    {
        $reader = IOFactory::createReader($readerType);

        if (method_exists($reader, 'setReadDataOnly') && ! str_contains(strtolower($readerType), 'html')) {
            $reader->setReadDataOnly(true);
        }

        return $reader;
    }

    private function flattenRows(array $rows): array
    {
        return array_values(array_filter(array_merge(...$rows), fn (?string $value) => $value !== null && $value !== ''));
    }

    private function looksLikeRekapPirt(array $headers): bool
    {
        return $this->contains($headers, 'no sppirt')
            && $this->contains($headers, 'nama branding produk')
            && $this->containsAny($headers, ['data produk pangan', 'kategori pangan'])
            && $this->contains($headers, 'jenis pangan')
            && $this->contains($headers, 'nib')
            && $this->contains($headers, 'nama pelaku usaha')
            && $this->contains($headers, 'alamat');
    }

    private function looksLikeCommitmentStatus(array $headers): bool
    {
        return $this->contains($headers, 'no sppirt')
            && $this->contains($headers, 'nib')
            && $this->contains($headers, 'verifikasi produk')
            && $this->contains($headers, 'verifikasi label')
            && $this->contains($headers, 'pkp')
            && $this->contains($headers, 'cppob')
            && $this->containsAny($headers, [
                'status pemenuhan komitmen',
                'status pememnuhan komitmen',
                'status pememnuhan',
            ]);
    }

    private function contains(array $headers, string $needle): bool
    {
        foreach ($headers as $header) {
            if (str_contains($header, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function containsAny(array $headers, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($this->contains($headers, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeHeader(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(mb_strtolower((string) $value));

        if ($value === '') {
            return null;
        }

        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value) ?: $value;
        $value = preg_replace('/\s+/', ' ', $value) ?: $value;

        return trim($value);
    }
}
