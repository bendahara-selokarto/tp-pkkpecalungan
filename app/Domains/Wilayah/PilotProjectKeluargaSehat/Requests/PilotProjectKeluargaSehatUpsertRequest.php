<?php

namespace App\Domains\Wilayah\PilotProjectKeluargaSehat\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class PilotProjectKeluargaSehatUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul_laporan' => 'required|string|max:255',
            'dasar_hukum' => 'nullable|string',
            'pendahuluan' => 'nullable|string',
            'maksud_tujuan' => 'nullable|string',
            'pelaksanaan' => 'nullable|string',
            'dokumentasi' => 'nullable|string',
            'penutup' => 'nullable|string',
            'tahun_awal' => 'required|integer|min:2000|max:2100',
            'tahun_akhir' => 'required|integer|min:2000|max:2100|gte:tahun_awal',
            'values' => 'nullable|array',
            'values.*.section' => 'required|string|in:data_dukung,pilot_project',
            'values.*.cluster_code' => 'required|string|max:32',
            'values.*.indicator_code' => 'required|string|max:100',
            'values.*.indicator_label' => 'required|string|max:255',
            'values.*.year' => 'required|integer|min:2000|max:2100',
            'values.*.semester' => 'required|integer|in:1,2',
            'values.*.value' => 'required|integer|min:0',
            'values.*.evaluation_note' => 'nullable|string',
            'values.*.sort_order' => 'nullable|integer|min:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $rawValues = $this->input('values', []);
        if (! is_array($rawValues)) {
            $rawValues = [];
        }

        $normalizedValues = collect($rawValues)
            ->filter(static fn ($item): bool => is_array($item))
            ->map(function (array $item): array {
                return [
                    'section' => $this->normalizeSection($item['section'] ?? 'pilot_project'),
                    'cluster_code' => strtoupper(trim((string) ($item['cluster_code'] ?? ''))),
                    'indicator_code' => trim((string) ($item['indicator_code'] ?? '')),
                    'indicator_label' => trim((string) ($item['indicator_label'] ?? '')),
                    'year' => is_numeric($item['year'] ?? null) ? (int) $item['year'] : $item['year'] ?? null,
                    'semester' => $this->normalizeSemester($item['semester'] ?? 1),
                    'value' => is_numeric($item['value'] ?? null) ? (int) $item['value'] : $item['value'] ?? null,
                    'evaluation_note' => $this->normalizeTextOrNull($item['evaluation_note'] ?? null),
                    'sort_order' => is_numeric($item['sort_order'] ?? null) ? (int) $item['sort_order'] : null,
                ];
            })
            ->values()
            ->all();

        $this->merge([
            'judul_laporan' => trim((string) $this->input('judul_laporan', '')),
            'dasar_hukum' => $this->normalizeTextOrNull($this->input('dasar_hukum')),
            'pendahuluan' => $this->normalizeTextOrNull($this->input('pendahuluan')),
            'maksud_tujuan' => $this->normalizeTextOrNull($this->input('maksud_tujuan')),
            'pelaksanaan' => $this->normalizeTextOrNull($this->input('pelaksanaan')),
            'dokumentasi' => $this->normalizeTextOrNull($this->input('dokumentasi')),
            'penutup' => $this->normalizeTextOrNull($this->input('penutup')),
            'values' => $normalizedValues,
        ]);
    }

    private function normalizeSection(mixed $section): string
    {
        $normalized = strtolower(trim((string) $section));

        return in_array($normalized, ['data_dukung', 'pilot_project'], true)
            ? $normalized
            : 'pilot_project';
    }

    private function normalizeSemester(mixed $semester): int
    {
        if (is_int($semester) || (is_string($semester) && is_numeric($semester))) {
            return ((int) $semester) === 2 ? 2 : 1;
        }

        $normalized = strtoupper(trim((string) $semester));

        return $normalized === 'II' ? 2 : 1;
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
