<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'link',
        'description',
        'icon',
        'created_at',
        'updated_at'
    ];
}
