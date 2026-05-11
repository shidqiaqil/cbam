<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Role Constants
    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;
    const ROLE_SUPERADMIN = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_employee',
        'id_org_unit',
        'id_job_position',
        'id_department',
        'id_division',
        'team',
        'department',
        'division',
        'role',
        'sso_hash',
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

    // Role Checking
    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    // List Role
    public static function roles()
    {
        return [
            static::ROLE_USER => 'User',
            static::ROLE_ADMIN => 'Admin',
            static::ROLE_SUPERADMIN => 'Super Admin',
        ];
    }

    // Accessor Role Name
    public function getRoleNameAttribute()
    {
        if (! in_array($this->role, array_keys(static::roles()))) {
            throw new \Exception('Role [' . $this->role . '] is not defined');
        }

        return static::roles()[$this->role];
    }
}
