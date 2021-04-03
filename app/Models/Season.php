<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;
	//protected $table='seasons';
    public $timestamps = false;
    
    protected $fillable = [
        'year',
        'season',
        'season_name',
    ];
}
