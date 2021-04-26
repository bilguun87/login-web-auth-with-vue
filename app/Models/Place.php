<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];
    public $timestamps = false;

    public function records()
    {
    	return $this->hasMany(Record::class);
    }

    public function moves()
    {
    	return $this->hasMany(Move::class);
    }
}
