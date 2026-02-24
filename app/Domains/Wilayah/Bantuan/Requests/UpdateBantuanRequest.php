<?php

namespace App\Domains\Wilayah\Bantuan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBantuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date_format:Y-m-d',
            'asal_bantuan' => 'required|in:pusat,provinsi,kabupaten,pihak_ketiga,lainnya',
            'jenis_bantuan' => 'required|in:uang,barang',
            'jumlah' => 'required|numeric|min:0',
            'lokasi_penerima' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $tanggal = $this->input('tanggal', $this->input('received_date'));
        $asalBantuan = $this->normalizeAsalBantuan($this->input('asal_bantuan', $this->input('source')));
        $jenisBantuan = $this->normalizeJenisBantuan($this->input('jenis_bantuan', $this->input('category')));
        $jumlah = $this->input('jumlah', $this->input('amount'));
        $lokasiPenerima = $this->input('lokasi_penerima', $this->input('name'));
        $keterangan = $this->input('keterangan', $this->input('description'));

        $this->merge([
            'tanggal' => $tanggal,
            'asal_bantuan' => $asalBantuan,
            'jenis_bantuan' => $jenisBantuan,
            'jumlah' => $jumlah,
            'lokasi_penerima' => $lokasiPenerima,
            'keterangan' => $keterangan,
        ]);
    }

    private function normalizeJenisBantuan(mixed $value): string
    {
        $normalized = strtolower(trim((string) $value));
        if ($normalized === '') {
            return '';
        }

        if (in_array($normalized, ['uang', 'keuangan', 'dana'], true)) {
            return 'uang';
        }

        if (in_array($normalized, ['barang', 'natura'], true)) {
            return 'barang';
        }

        return $normalized;
    }

    private function normalizeAsalBantuan(mixed $value): string
    {
        $normalized = strtolower(trim((string) $value));
        if ($normalized === '') {
            return '';
        }

        return match ($normalized) {
            'pihak ketiga', 'pihak-ketiga' => 'pihak_ketiga',
            default => $normalized,
        };
    }
}
