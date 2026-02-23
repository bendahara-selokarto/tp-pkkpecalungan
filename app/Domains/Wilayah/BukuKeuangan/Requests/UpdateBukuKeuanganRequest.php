<?php

namespace App\Domains\Wilayah\BukuKeuangan\Requests;

use App\Domains\Wilayah\BukuKeuangan\Models\BukuKeuangan;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBukuKeuanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transaction_date' => 'required|date_format:Y-m-d',
            'source' => 'required|in:' . implode(',', BukuKeuangan::sources()),
            'description' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'entry_type' => 'required|in:' . implode(',', BukuKeuangan::entryTypes()),
            'amount' => 'required|numeric|min:0.01',
        ];
    }
}
