<?php

namespace App\Http\Requests\Arsip;

use App\Domains\Wilayah\Arsip\Models\ArsipDocument;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArsipDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $arsipDocument = $this->route('arsipDocument');

        if (! $arsipDocument instanceof ArsipDocument) {
            return false;
        }

        return (bool) $this->user()?->can('update', $arsipDocument);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
            'is_published' => 'nullable|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => filter_var($this->input('is_published', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
