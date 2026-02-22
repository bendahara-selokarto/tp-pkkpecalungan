<?php

namespace App\Domains\Wilayah\Inventaris\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInventarisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'asal_barang' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string|max:50',
            'tanggal_penerimaan' => 'nullable|date_format:Y-m-d',
            'tempat_penyimpanan' => 'nullable|string|max:255',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
        ];
    }
}
