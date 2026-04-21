<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'commission_percentage',
        'theme',
        'menu_permissions',
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
            'menu_permissions' => 'array',
        ];
    }

    public function hasMenuPermission(string $menuKey, string $action = 'view'): bool
    {
        // Admin role always has all permissions
        if ($this->role === 'Admin') {
            return true;
        }

        $permissions = $this->menu_permissions;

        if (empty($permissions)) {
            // Default to no access for non-admins if permissions are strictly defined
            return false;
        }

        // Backward compatibility: If the array is simple [0 => 'key', 1 => 'key']
        if (array_is_list($permissions)) {
            return in_array($menuKey, $permissions, true);
        }

        // New nested structure: ['menu_key' => ['view' => true, 'edit' => false, 'delete' => false]]
        if (isset($permissions[$menuKey])) {
            $menuPerms = $permissions[$menuKey];
            return isset($menuPerms[$action]) && (bool)$menuPerms[$action];
        }

        return false;
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function commissionPayments()
    {
        return $this->hasMany(CommissionPayment::class);
    }
}
