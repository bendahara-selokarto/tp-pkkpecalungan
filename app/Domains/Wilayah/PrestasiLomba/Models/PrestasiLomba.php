<?php

namespace App\Domains\Wilayah\PrestasiLomba\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PrestasiLomba extends Model
{
    private const ROLE_TO_GROUP_MAP = [
        'desa-sekretaris' => 'sekretaris-tpk',
        'kecamatan-sekretaris' => 'sekretaris-tpk',
        'desa-bendahara' => 'bendahara-tpk',
        'kecamatan-bendahara' => 'bendahara-tpk',
        'desa-pokja-i' => 'pokja-i',
        'desa-pokja-ii' => 'pokja-ii',
        'desa-pokja-iii' => 'pokja-iii',
        'desa-pokja-iv' => 'pokja-iv',
        'kecamatan-pokja-i' => 'pokja-i',
        'kecamatan-pokja-ii' => 'pokja-ii',
        'kecamatan-pokja-iii' => 'pokja-iii',
        'kecamatan-pokja-iv' => 'pokja-iv',
    ];

    protected $table = 'prestasi_lombas';

    protected $fillable = [
        'tahun',
        'jenis_lomba',
        'lokasi',
        'prestasi_kecamatan',
        'prestasi_kabupaten',
        'prestasi_provinsi',
        'prestasi_nasional',
        'keterangan',
        'tahun_anggaran',
        'level',
        'group',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tahun' => 'integer',
            'prestasi_kecamatan' => 'boolean',
            'prestasi_kabupaten' => 'boolean',
            'prestasi_provinsi' => 'boolean',
            'prestasi_nasional' => 'boolean',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PrestasiLomba $prestasiLomba): void {
            if (! is_string($prestasiLomba->group) || $prestasiLomba->group === '') {
                $prestasiLomba->group = self::resolveGroupFromCreator((int) $prestasiLomba->created_by);
            }

            if (is_numeric($prestasiLomba->tahun_anggaran)) {
                return;
            }

            $prestasiLomba->tahun_anggaran = is_numeric($prestasiLomba->tahun)
                ? (int) $prestasiLomba->tahun
                : (int) now()->format('Y');
        });
    }

    private static function resolveGroupFromCreator(int $creatorId): string
    {
        if ($creatorId <= 0) {
            return 'pokja-i';
        }

        $creator = User::query()->find($creatorId);
        if (! $creator instanceof User) {
            return 'pokja-i';
        }

        foreach ($creator->getRoleNames()->all() as $roleName) {
            if (isset(self::ROLE_TO_GROUP_MAP[$roleName])) {
                return self::ROLE_TO_GROUP_MAP[$roleName];
            }
        }

        return 'pokja-i';
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
