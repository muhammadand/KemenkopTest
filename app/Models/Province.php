<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use SoftDeletes;

    // Nama table
    protected $table = 'provinces';

    // Primary key manual
    protected $primaryKey = 'id';
    public $incrementing = false; // karena id manual
    protected $keyType = 'integer';

    // Mass assignable
    protected $fillable = [
 
        'name',
        'code',
    ];

    // Soft delete column
    protected $dates = ['deleted_at'];
}
