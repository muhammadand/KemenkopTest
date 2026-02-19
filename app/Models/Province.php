<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Province extends Model
{
    use  HasUuids;

    protected $table = 'provinces';

    protected $primaryKey = 'province_id';
    public $incrementing = false;
    protected $keyType = 'string';                                                                              
    protected $fillable = [
        'name',
        'code',
    ];
      public function news()
    {
        return $this->hasMany(
            News::class,    
        );
    }
}
