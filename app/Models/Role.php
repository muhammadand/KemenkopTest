<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relationship ke RolePosition
     */
    public function positions()
    {
        return $this->hasMany(RolePosition::class);
    }

    /**
     * Relationship ke User melalui pivot role_user
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
