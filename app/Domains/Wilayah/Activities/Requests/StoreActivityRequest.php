<?php

namespace App\Domains\Wilayah\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'nama_petugas' => 'nullable|string|max:255',
            'jabatan_petugas' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'uraian' => 'nullable|string',
            'activity_date' => 'required|date_format:Y-m-d',
            'tempat_kegiatan' => 'nullable|string|max:255',
            'tanda_tangan' => 'nullable|string|max:255',
            'additional_info' => 'nullable|array',
            'additional_info.program_category' => 'nullable|string|max:255',
            'additional_info.volume' => 'nullable|integer|min:1',
            'additional_info.sasaran' => 'nullable|string|max:255',
            'additional_info.metode' => 'nullable|string|max:255',
            'additional_info.jenis_literasi' => 'nullable|string|max:255',
            'additional_info.jenis_kejar_paket' => 'nullable|string|max:255',
            'additional_info.jenis_koperasi' => 'nullable|string|max:255',
            'additional_info.kategori_pangan' => 'nullable|string|max:255',
            'additional_info.kategori_sandang' => 'nullable|string|max:255',
            'additional_info.kategori_perumahan' => 'nullable|string|max:255',
            'additional_info.jenis_layanan_kesehatan' => 'nullable|string|max:255',
            'additional_info.nama_posyandu' => 'nullable|string|max:255',
            'additional_info.perencanaan_sehat' => 'nullable|string|max:255',
            'image_upload' => 'nullable|file|image|max:5120',
            'document_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp|max:10240',
        ];
    }
}
