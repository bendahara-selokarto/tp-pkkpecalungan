<?php

namespace App\Domains\Wilayah\Paar\Requests;

use App\Domains\Wilayah\Paar\Models\Paar;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'indikator' => ['required', 'string', Rule::in(Paar::indicatorKeys())],
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:255',
        ];
    }
}