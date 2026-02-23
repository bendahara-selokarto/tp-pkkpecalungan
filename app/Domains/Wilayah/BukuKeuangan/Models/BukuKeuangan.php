<?php

namespace App\Domains\Wilayah\BukuKeuangan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BukuKeuangan extends Model
{
    public const ENTRY_TYPE_PEMASUKAN = 'pemasukan';
    public const ENTRY_TYPE_PENGELUARAN = 'pengeluaran';
    public const ENTRY_TYPES = [
        self::ENTRY_TYPE_PEMASUKAN,
        self::ENTRY_TYPE_PENGELUARAN,
    ];

    public const SOURCE_KAS_TUNAI = 'kas_tunai';
    public const SOURCE_BANK = 'bank';
    public const SOURCE_PUSAT = 'pusat';
    public const SOURCE_PROVINSI = 'provinsi';
    public const SOURCE_KABUPATEN = 'kabupaten';
    public const SOURCE_PIHAK_KETIGA = 'pihak_ketiga';
    public const SOURCE_SWADAYA = 'swadaya';
    public const SOURCE_LAINNYA = 'lainnya';
    public const SOURCES = [
        self::SOURCE_KAS_TUNAI,
        self::SOURCE_BANK,
        self::SOURCE_PUSAT,
        self::SOURCE_PROVINSI,
        self::SOURCE_KABUPATEN,
        self::SOURCE_PIHAK_KETIGA,
        self::SOURCE_SWADAYA,
        self::SOURCE_LAINNYA,
    ];

    protected $table = 'buku_keuangans';

    protected $fillable = [
        'transaction_date',
        'source',
        'description',
        'reference_number',
        'entry_type',
        'amount',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date:Y-m-d',
            'amount' => 'decimal:2',
        ];
    }

    public static function entryTypes(): array
    {
        return self::ENTRY_TYPES;
    }

    public static function sources(): array
    {
        return self::SOURCES;
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
