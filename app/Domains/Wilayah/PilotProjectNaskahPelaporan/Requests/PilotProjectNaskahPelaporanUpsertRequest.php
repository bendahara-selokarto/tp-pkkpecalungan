<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class PilotProjectNaskahPelaporanUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul_laporan' => 'required|string|max:255',
            'surat_kepada' => 'nullable|string|max:500',
            'surat_dari' => 'nullable|string|max:500',
            'surat_tembusan' => 'nullable|string|max:500',
            'surat_tanggal' => 'nullable|date',
            'surat_nomor' => 'nullable|string|max:150',
            'surat_sifat' => 'nullable|string|max:150',
            'surat_lampiran' => 'nullable|string|max:255',
            'surat_hal' => 'nullable|string|max:500',
            'dasar_pelaksanaan' => 'required|string',
            'pendahuluan' => 'required|string',
            'pelaksanaan_1' => 'required|string',
            'pelaksanaan_2' => 'required|string',
            'pelaksanaan_3' => 'required|string',
            'pelaksanaan_4' => 'required|string',
            'pelaksanaan_5' => 'required|string',
            'penutup' => 'required|string',
            'lampiran_6a_foto' => 'nullable|array',
            'lampiran_6a_foto.*' => 'file|image|max:5120',
            'lampiran_6b_foto' => 'nullable|array',
            'lampiran_6b_foto.*' => 'file|image|max:5120',
            'lampiran_6d_dokumen' => 'nullable|array',
            'lampiran_6d_dokumen.*' => 'file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'lampiran_6e_foto' => 'nullable|array',
            'lampiran_6e_foto.*' => 'file|image|max:5120',
            'remove_attachment_ids' => 'nullable|array',
            'remove_attachment_ids.*' => 'integer|min:1',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'judul_laporan' => trim((string) $this->input('judul_laporan', '')),
            'surat_kepada' => trim((string) $this->input('surat_kepada', '')),
            'surat_dari' => trim((string) $this->input('surat_dari', '')),
            'surat_tembusan' => trim((string) $this->input('surat_tembusan', '')),
            'surat_tanggal' => $this->filled('surat_tanggal')
                ? $this->input('surat_tanggal')
                : null,
            'surat_nomor' => trim((string) $this->input('surat_nomor', '')),
            'surat_sifat' => trim((string) $this->input('surat_sifat', '')),
            'surat_lampiran' => trim((string) $this->input('surat_lampiran', '')),
            'surat_hal' => trim((string) $this->input('surat_hal', '')),
            'dasar_pelaksanaan' => trim((string) $this->input('dasar_pelaksanaan', '')),
            'pendahuluan' => trim((string) $this->input('pendahuluan', '')),
            'pelaksanaan_1' => trim((string) $this->input('pelaksanaan_1', '')),
            'pelaksanaan_2' => trim((string) $this->input('pelaksanaan_2', '')),
            'pelaksanaan_3' => trim((string) $this->input('pelaksanaan_3', '')),
            'pelaksanaan_4' => trim((string) $this->input('pelaksanaan_4', '')),
            'pelaksanaan_5' => trim((string) $this->input('pelaksanaan_5', '')),
            'penutup' => trim((string) $this->input('penutup', '')),
            'remove_attachment_ids' => is_array($this->input('remove_attachment_ids'))
                ? array_values($this->input('remove_attachment_ids'))
                : [],
        ]);
    }
}
