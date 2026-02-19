<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
     use HasFactory;
    //table
    protected $table='news';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'sub_title',
        'content',
        'province_id'

    ];

    public function province()
    {
        return $this->belongsTo(Province::class,'province_id','province_id');
    }

}
