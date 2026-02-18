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
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'source' => 'required|in:pusat,provinsi,kabupaten,pihak_ketiga,lainnya',
            'amount' => 'required|numeric|min:0',
            'received_date' => 'required|date',
        ];
    }
}
