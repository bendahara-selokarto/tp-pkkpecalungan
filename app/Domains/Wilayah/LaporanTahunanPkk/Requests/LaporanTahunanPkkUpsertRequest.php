<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class LaporanTahunanPkkUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul_laporan' => 'required|string|max:255',
            'tahun_laporan' => 'required|integer|min:2000|max:2100',
            'pendahuluan' => 'nullable|string',
            'keberhasilan' => 'nullable|string',
            'hambatan' => 'nullable|string',
            'kesimpulan' => 'nullable|string',
            'penutup' => 'nullable|string',
            'disusun_oleh' => 'nullable|string|max:255',
            'jabatan_penanda_tangan' => 'nullable|string|max:255',
            'nama_penanda_tangan' => 'nullable|string|max:255',
            'manual_entries' => 'nullable|array',
            'manual_entries.*.bidang' => 'required|string|in:sekretariat,pokja-i,pokja-ii,pokja-iii,pokja-iv',
            'manual_entries.*.activity_date' => 'nullable|date_format:Y-m-d',
            'manual_entries.*.description' => 'required|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $rawEntries = $this->input('manual_entries', []);
        if (! is_array($rawEntries)) {
            $rawEntries = [];
        }

        $entries = collect($rawEntries)
            ->filter(static fn ($item): bool => is_array($item))
            ->map(function (array $item): array {
                $bidang = strtolower(trim((string) ($item['bidang'] ?? 'sekretariat')));
                if (! in_array($bidang, ['sekretariat', 'pokja-i', 'pokja-ii', 'pokja-iii', 'pokja-iv'], true)) {
                    $bidang = 'sekretariat';
                }

                $description = trim((string) ($item['description'] ?? ''));
                $activityDate = $item['activity_date'] ?? null;
                if (is_string($activityDate)) {
                    $activityDate = trim($activityDate);
                }
                if ($activityDate === '') {
                    $activityDate = null;
                }

                return [
                    'bidang' => $bidang,
                    'activity_date' => $activityDate,
                    'description' => $description,
                ];
            })
            ->filter(static fn (array $item): bool => $item['description'] !== '')
            ->values()
            ->all();

        $this->merge([
            'judul_laporan' => trim((string) $this->input('judul_laporan', '')),
            'pendahuluan' => $this->normalizeTextOrNull($this->input('pendahuluan')),
            'keberhasilan' => $this->normalizeTextOrNull($this->input('keberhasilan')),
            'hambatan' => $this->normalizeTextOrNull($this->input('hambatan')),
            'kesimpulan' => $this->normalizeTextOrNull($this->input('kesimpulan')),
            'penutup' => $this->normalizeTextOrNull($this->input('penutup')),
            'disusun_oleh' => $this->normalizeTextOrNull($this->input('disusun_oleh')),
            'jabatan_penanda_tangan' => $this->normalizeTextOrNull($this->input('jabatan_penanda_tangan')),
            'nama_penanda_tangan' => $this->normalizeTextOrNull($this->input('nama_penanda_tangan')),
            'manual_entries' => $entries,
        ]);
    }

    private function normalizeTextOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $normalized = trim($value);

        return $normalized === '' ? null : $normalized;
    }
}

