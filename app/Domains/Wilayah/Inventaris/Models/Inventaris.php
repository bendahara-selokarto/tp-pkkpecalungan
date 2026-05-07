<?php

namespace App\Domains\Wilayah\Inventaris\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
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

    protected $table = 'inventaris';

    protected $fillable = [
        'name',
        'asal_barang',
        'description',
        'keterangan',
        'quantity',
        'unit',
        'tanggal_penerimaan',
        'tempat_penyimpanan',
        'condition',
        'level',
        'group',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $inventaris): void {
            if (! is_string($inventaris->group) || $inventaris->group === '') {
                $inventaris->group = self::resolveGroupFromCreator((int) $inventaris->created_by);
            }

            if (is_numeric($inventaris->tahun_anggaran)) {
                return;
            }

            $fallbackYear = $inventaris->tanggal_penerimaan
                ? (int) date('Y', strtotime((string) $inventaris->tanggal_penerimaan))
                : (int) now()->format('Y');

            $inventaris->tahun_anggaran = $fallbackYear;
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
