<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMatrix extends Model
{
    use HasFactory;
    protected $table = "groupmatrix";
    public $timestamps = false;
    protected $fillable = [
        'br_id',
        'br_name',
        'pos_id',
        'pos_name',
        'objectguid',
        'cn'
    ];
}
