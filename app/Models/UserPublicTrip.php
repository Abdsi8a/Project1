<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPublicTrip extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'tripPoint_id',
        'numberOfTicket',
        'price',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function tripPoint(){
        return $this->belongsTo(TripPoint::class);
    }
}
