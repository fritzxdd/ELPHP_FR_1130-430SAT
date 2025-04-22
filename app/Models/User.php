<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'users_id';
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'users_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'users_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class, 'users_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'users_id');
    }

    public function authTokens()
    {
        return $this->hasMany(AuthToken::class, 'user_id');
    }
}
