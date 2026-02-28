<?php

namespace App\Domains\Wilayah\Arsip\Models;

use Database\Factories\ArsipDocumentFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'is_published',
        'published_at',
        'download_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
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

    protected static function newFactory(): ArsipDocumentFactory
    {
        return ArsipDocumentFactory::new();
    }
}
