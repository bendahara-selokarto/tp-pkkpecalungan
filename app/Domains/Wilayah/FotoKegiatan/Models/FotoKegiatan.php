<?php

namespace App\Domains\Wilayah\FotoKegiatan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FotoKegiatan extends Model
{
    private const ROLE_TO_GROUP_MAP = [
        'desa-pokja-ii' => 'pokja-ii',
        'desa-pokja-iii' => 'pokja-iii',
        'desa-pokja-iv' => 'pokja-iv',
        'kecamatan-pokja-ii' => 'pokja-ii',
        'kecamatan-pokja-iii' => 'pokja-iii',
        'kecamatan-pokja-iv' => 'pokja-iv',
    ];

    protected $table = 'foto_kegiatans';

    protected $fillable = [
        'activity_date',
        'title',
        'description',
        'image_path',
        'level',
        'group',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date:Y-m-d',
            'tahun_anggaran' => 'integer',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return asset('storage/' . $this->image_path);
    }

    protected static function booted(): void
    {
        static::creating(function (self $fotoKegiatan): void {
            if (! is_string($fotoKegiatan->group) || $fotoKegiatan->group === '') {
                $fotoKegiatan->group = self::resolveGroupFromCreator((int) $fotoKegiatan->created_by);
            }

            if (! is_numeric($fotoKegiatan->tahun_anggaran)) {
                $fotoKegiatan->tahun_anggaran = is_string($fotoKegiatan->activity_date) && $fotoKegiatan->activity_date !== ''
                    ? (int) date('Y', strtotime($fotoKegiatan->activity_date))
                    : (int) Carbon::now()->format('Y');
            }
        });
    }

    private static function resolveGroupFromCreator(int $creatorId): string
    {
        if ($creatorId <= 0) {
            return 'pokja-ii';
        }

        $creator = User::query()->find($creatorId);
        if (! $creator instanceof User) {
            return 'pokja-ii';
        }

        foreach ($creator->getRoleNames()->all() as $roleName) {
            if (isset(self::ROLE_TO_GROUP_MAP[$roleName])) {
                return self::ROLE_TO_GROUP_MAP[$roleName];
            }
        }

        return 'pokja-ii';
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
