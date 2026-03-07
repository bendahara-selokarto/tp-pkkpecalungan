<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Domains\Wilayah\Enums\ScopeLevel;
use App\Domains\Wilayah\Models\Area;
use App\Support\RoleScopeMatrix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'area_id',
        'scope',
        'active_budget_year',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active_budget_year' => 'integer',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (! is_numeric($user->active_budget_year)) {
                $user->active_budget_year = (int) now()->format('Y');
            }
        });
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function isKecamatan(): bool
    {
        return $this->scope === ScopeLevel::KECAMATAN->value;
    }

    public function isDesa(): bool
    {
        return $this->scope === ScopeLevel::DESA->value;
    }

    public function hasRoleForScope(string $scope): bool
    {
        return RoleScopeMatrix::userHasRoleForScope($this, $scope);
    }
}
