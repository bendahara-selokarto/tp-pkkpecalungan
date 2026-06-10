<?php

namespace App\Domains\Wilayah\Activities\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Activity extends Model
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

    protected $table = 'activities';

    protected $fillable = [
        'title',
        'nama_petugas',
        'jabatan_petugas',
        'description',
        'uraian',
        'level',
        'group',
        'additional_info',
        'area_id',
        'created_by',
        'tahun_anggaran',
        'activity_date',
        'tempat_kegiatan',
        'status',
        'tanda_tangan',
        'image_path',
        'document_path',
    ];

    protected function casts(): array
    {
        return [
            'tahun_anggaran' => 'integer',
            'additional_info' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $activity): void {
            if (! is_string($activity->group) || $activity->group === '') {
                $activity->group = self::resolveGroupFromCreator((int) $activity->created_by);
            }

            if (is_numeric($activity->tahun_anggaran)) {
                return;
            }

            $activity->tahun_anggaran = is_string($activity->activity_date) && $activity->activity_date !== ''
                ? (int) date('Y', strtotime($activity->activity_date))
                : (int) Carbon::now()->format('Y');
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
