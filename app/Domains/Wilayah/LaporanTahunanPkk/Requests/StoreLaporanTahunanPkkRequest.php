<?php

namespace App\Domains\Wilayah\LaporanTahunanPkk\Requests;

use Illuminate\Validation\Rule;

class StoreLaporanTahunanPkkRequest extends LaporanTahunanPkkUpsertRequest
{
    public function rules(): array
    {
        $user = $this->user();
        $level = (string) $this->segment(1);
        $areaId = is_numeric($user?->area_id) ? (int) $user->area_id : 0;

        $rules = parent::rules();
        $rules['tahun_laporan'] = [
            'required',
            'integer',
            'min:2000',
            'max:2100',
            Rule::unique('laporan_tahunan_pkk_reports', 'tahun_laporan')
                ->where(fn ($query) => $query->where('level', $level)->where('area_id', $areaId)),
        ];

        return $rules;
    }
}
