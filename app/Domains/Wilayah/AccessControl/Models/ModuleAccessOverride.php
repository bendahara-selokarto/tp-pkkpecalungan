<?php

namespace App\Domains\Wilayah\AccessControl\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleAccessOverride extends Model
{
    protected $fillable = [
        'scope',
        'role_name',
        'module_slug',
        'mode',
        'changed_by',
    ];

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(ModuleAccessOverrideAudit::class, 'module_access_override_id');
    }
}

