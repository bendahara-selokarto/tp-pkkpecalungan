<?php

namespace App\Domains\Wilayah\KaderKhusus\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class KaderKhusus extends Model
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

    protected $table = 'kader_khusus';

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status_perkawinan',
        'alamat',
        'pendidikan',
        'jenis_kader_khusus',
        'keterangan',
        'level',
        'group',
        'area_id',
        'created_by',
        'tahun_anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tahun_anggaran' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $kaderKhusus): void {
            if (! is_string($kaderKhusus->group) || $kaderKhusus->group === '') {
                $kaderKhusus->group = self::resolveGroupFromCreator((int) $kaderKhusus->created_by);
            }

            if (is_numeric($kaderKhusus->tahun_anggaran)) {
                return;
            }

            $kaderKhusus->tahun_anggaran = (int) now()->format('Y');
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

    protected $appends = [
        'umur',
    ];

    public function getUmurAttribute(): ?int
    {
        if (! $this->tanggal_lahir) {
            return null;
        }

        return Carbon::parse($this->tanggal_lahir)->age;
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
