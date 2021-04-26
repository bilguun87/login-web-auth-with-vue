<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $fillable = [
        'type_id',
        'place_id',
        'name',
        'ogson',
        'awsan',
        'date',
        'isAvailable'
    ];

    public function type()
    {
    	return $this->belongsTo(Type::class);
    }

    public function place()
    {
    	return $this->belongsTo(Place::class);
    }

    public function moves()
    {
    	return $this->hasMany(Move::class);
    }
}
