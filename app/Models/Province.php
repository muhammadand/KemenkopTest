<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Province extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'provinces';

    protected $primaryKey = 'province_id';

    public $incrementing = false;   // UUID bukan auto increment
    protected $keyType = 'string';  // UUID adalah string

    protected $fillable = [
        'name',
        'code',
    ];
}
