<?php

namespace App\Domains\Wilayah\TutorKhusus\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTutorKhususRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_tutor' => 'required|string|in:kf,paud',
            'jumlah_tutor' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }
}
