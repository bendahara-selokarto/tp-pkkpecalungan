<?php

namespace App\Domains\Wilayah\Simulasi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListSimulasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
            'tahun_anggaran' => ['nullable', 'integer'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->get('perPage', 15);
    }
}
