<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowedUser extends Model
{
    use HasFactory;
    protected $table = 'allowed_users';
    protected $fillable = ['domain','created_at','updated_at'];
}
