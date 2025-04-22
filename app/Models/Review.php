<?php   

// app/Models/Review.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $primaryKey = 'reviews_id';
    protected $fillable = [
        'users_id', 
        'vehicles_id', 
        'rating', 
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicles_id');
    }
}