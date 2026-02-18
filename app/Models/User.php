<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ===========================
    // Relasi ke Role melalui pivot role_user
    // ===========================
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,     // Model target
            'role_user',     // Nama tabel pivot
            'user_id',       // Foreign key di pivot untuk User
            'role_id'        // Foreign key di pivot untuk Role
        );
    }

    // ===========================
    // Optional: ambil semua positions user
    // ===========================
    public function rolePositions()
    {
        return $this->hasManyThrough(
            RolePosition::class, // Target model
            Role::class,         // Intermediate model
            'id',                // Foreign key Role di RolePosition
            'role_id',           // Foreign key RolePosition di User melalui Role
            'id',                // Local key User
            'id'                 // Local key Role
        );
    }
}
