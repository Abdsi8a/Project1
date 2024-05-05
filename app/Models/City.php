<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'country',
    ];
    public function trip(){
        return $this->hasMany(Trip::class);
    }
    public function cityHotel(){
        return $this->hasMany(CitiesHotel::class);
    }
    public function tripPoint(){
        return $this->hasMany(TripPoints::class);
    }
    public function airPort(){
        return $this->hasMany(Airport::class);
    }
    public function torismPlaces(){
        return $this->hasMany(TourismPlace::class);
    }
}
