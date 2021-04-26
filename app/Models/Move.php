<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    use HasFactory;
    protected $fillable = [
        'record_id',
        'place_id',
        'requester_name',
        'performer_name',
        'move_start_date',
        'move_end_date',
        'description'
    ];
    public $timestamps = false;

    public function record()
    {
    	return $this->belongsTo(Record::class);
    }

    public function place()
    {
    	return $this->belongsTo(Place::class);
    }
}
