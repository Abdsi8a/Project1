<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicTrip extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'image',
        'description',
        'citiesHotel_id',
        'dateOfTrip',
        'dateEndOfTrip',
        'discountType',
        'display',
    ];

    protected $hidden=[
        'created_at',
        'updated_at',
    ];
    public function citiesHotel(){
        return $this->belongsTo(CitiesHotel::class);
    }
    public function tripPoint(){
        return $this->hasMany(TripPoint::class,'publicTrip_id');
    }
    public function attraction(){
        return $this->hasMany(Attraction::class);
    }
    public function publicTripPlace(){
        return $this->hasMany(PublicTripPlace::class);
    }
    public function publicTripClassification(){
        return $this->hasMany(PublicTripClassification::class);
    }

}
