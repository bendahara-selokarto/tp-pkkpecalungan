<?php

namespace App\Http\Requests\Arsip;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use Illuminate\Foundation\Http\FormRequest;

class StoreArsipDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', ArsipDocument::class);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
        ];
    }
}
