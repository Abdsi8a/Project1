<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable=[
    'airline_id',
    'airport_id1',
    'airport_id2',
    'typeOfTicket',
    'roundOrOne_trip',
    'timeOfticket',
    'duration',
    'price',
    'numOfTickets',
    'dateEndOfTicket',
    'dateOfTicket',
    ];
    
    protected $hidden=[
        'created_at',
        'updated_at',
    ];

    public function airPort(){
        return $this->belongsTo(Airport::class);
    }
    public function airLine(){
        return $this->belongsTo(Airline::class,'airline_id');
    }
    public function bookingTicket(){
        return $this->hasOne(BookingTicket::class);
    }

}
