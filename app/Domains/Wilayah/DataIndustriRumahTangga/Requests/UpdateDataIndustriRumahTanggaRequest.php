<?php

namespace App\Domains\Wilayah\DataIndustriRumahTangga\Requests;

use App\Domains\Wilayah\DataIndustriRumahTangga\Models\DataIndustriRumahTangga;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDataIndustriRumahTanggaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_jenis_industri' => ['required', 'string', Rule::in(DataIndustriRumahTangga::kategoriJenisIndustriOptions())],
            'komoditi' => 'required|string|max:255',
            'jumlah_komoditi' => 'required|string|max:100',
        ];
    }
}




