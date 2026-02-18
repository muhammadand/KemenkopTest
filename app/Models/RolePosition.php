<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePosition extends Model
{
    use HasFactory;

    protected $table = 'role_positions';

    protected $fillable = [
        'position',
        'role_id',
    ];

    /**
     * Relationship ke tabel roles
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship ke tabel role_user jika perlu nanti
     * (opsional, tergantung apakah ingin akses dari RolePosition ke Users)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
