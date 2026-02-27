<?php

namespace App\Domains\Wilayah\BukuDaftarHadir\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuDaftarHadirRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_date' => 'required|date_format:Y-m-d',
            'activity_id' => 'required|integer|exists:activities,id',
            'attendee_name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
