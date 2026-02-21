<?php

namespace App\Domains\Wilayah\PilotProjectNaskahPelaporan\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotProjectNaskahPelaporanAttachment extends Model
{
    public const CATEGORY_6A_PHOTO = '6a_photo';
    public const CATEGORY_6B_PHOTO = '6b_photo';
    public const CATEGORY_6D_DOCUMENT = '6d_document';
    public const CATEGORY_6E_PHOTO = '6e_photo';

    protected $table = 'pilot_project_naskah_pelaporan_attachments';

    protected $fillable = [
        'report_id',
        'category',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'level',
        'area_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(PilotProjectNaskahPelaporanReport::class, 'report_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
