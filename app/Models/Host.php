<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table="hosts";
    protected $fillable = [
        'ip',
        'description',
        'group_id'
    ];
}
