<?php

namespace App\Domains\Wilayah\TamanBacaan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTamanBacaanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_taman_bacaan' => 'required|string|max:255',
            'nama_pengelola' => 'required|string|max:255',
            'jumlah_buku_bacaan' => 'required|string|max:255',
            'jenis_buku' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'jumlah' => 'required|string|max:255',
        ];
    }
}


