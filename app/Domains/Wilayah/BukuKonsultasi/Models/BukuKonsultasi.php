<?php

namespace App\Domains\Wilayah\BukuKonsultasi\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BukuKonsultasi extends Model
{
    private const ROLE_TO_GROUP_MAP = [
        'desa-sekretaris' => 'sekretaris-tpk',
        'kecamatan-sekretaris' => 'sekretaris-tpk',
        'desa-pokja-iii' => 'pokja-iii',
        'kecamatan-pokja-iii' => 'pokja-iii',
    ];

    protected $table = 'buku_konsultasis';

    protected $fillable = [
        'activity_date',
        'description',
        'disposition',
        'level',
        'group',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date:Y-m-d',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $bukuKonsultasi): void {
            if (! is_string($bukuKonsultasi->group) || $bukuKonsultasi->group === '') {
                $bukuKonsultasi->group = self::resolveGroupFromCreator((int) $bukuKonsultasi->created_by);
            }

            if (! is_numeric($bukuKonsultasi->tahun_anggaran)) {
                $bukuKonsultasi->tahun_anggaran = is_string($bukuKonsultasi->activity_date) && $bukuKonsultasi->activity_date !== ''
                    ? (int) date('Y', strtotime($bukuKonsultasi->activity_date))
                    : (int) Carbon::now()->format('Y');
            }
        });
    }

    private static function resolveGroupFromCreator(int $creatorId): string
    {
        if ($creatorId <= 0) {
            return 'sekretaris-tpk';
        }

        $creator = User::query()->find($creatorId);
        if (! $creator instanceof User) {
            return 'sekretaris-tpk';
        }

        foreach ($creator->getRoleNames()->all() as $roleName) {
            if (isset(self::ROLE_TO_GROUP_MAP[$roleName])) {
                return self::ROLE_TO_GROUP_MAP[$roleName];
            }
        }

        return 'sekretaris-tpk';
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
