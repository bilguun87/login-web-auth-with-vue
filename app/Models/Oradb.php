<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oradb extends Model
{
    use HasFactory;
    protected $table = "oradb";
    protected $fillable = [
        'name',
        'constr',
        'user',
        'description',
        'created_at',
        'updated_at'
    ];
}
