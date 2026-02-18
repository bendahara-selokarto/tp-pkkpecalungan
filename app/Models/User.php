<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function isKecamatan(): bool
    {
        return $this->scope === 'kecamatan';
    }

    public function isDesa(): bool
    {
        return $this->scope === 'desa';
    }

    public function hasRoleForScope(string $scope): bool
    {
        return RoleScopeMatrix::userHasRoleForScope($this, $scope);
    }
}
