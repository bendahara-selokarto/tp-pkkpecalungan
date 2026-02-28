<?php

namespace App\Domains\Wilayah\Arsip\Models;

use App\Domains\Wilayah\Models\Area;
use App\Models\User;
use Database\Factories\ArsipDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipDocument extends Model
{
    use HasFactory;

    protected $table = 'arsip_documents';

    protected $fillable = [
        'title',
        'description',
        'original_name',
        'file_path',
        'mime_type',
        'extension',
        'size_bytes',
        'is_global',
        'level',
        'area_id',
        'download_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'size_bytes' => 'integer',
        'download_count' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    protected static function newFactory(): ArsipDocumentFactory
    {
        return ArsipDocumentFactory::new();
    }
}
