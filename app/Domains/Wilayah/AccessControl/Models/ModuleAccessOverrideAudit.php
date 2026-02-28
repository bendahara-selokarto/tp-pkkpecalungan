<?php

namespace App\Domains\Wilayah\AccessControl\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleAccessOverrideAudit extends Model
{
    protected $fillable = [
        'module_access_override_id',
        'scope',
        'role_name',
        'module_slug',
        'before_mode',
        'after_mode',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function override(): BelongsTo
    {
        return $this->belongsTo(ModuleAccessOverride::class, 'module_access_override_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

