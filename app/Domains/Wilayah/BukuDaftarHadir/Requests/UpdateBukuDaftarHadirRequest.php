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
            'title' => 'required|string|max:255',
            'attendance_date' => 'nullable|date_format:Y-m-d',
            'activity_id' => 'nullable|integer|exists:activities,id',
            'attendee_name' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }
}
