<?php

namespace App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Requests;

use App\Domains\Wilayah\DataPemanfaatanTanahPekaranganHatinyaPkk\Models\DataPemanfaatanTanahPekaranganHatinyaPkk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDataPemanfaatanTanahPekaranganHatinyaPkkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_pemanfaatan_lahan' => ['required', 'string', Rule::in(DataPemanfaatanTanahPekaranganHatinyaPkk::kategoriPemanfaatanLahanOptions())],
            'komoditi' => 'required|string|max:255',
            'jumlah_komoditi' => 'required|string|max:100',
        ];
    }
}



