<?php

namespace App\Domains\Wilayah\Kliping\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKlipingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
