<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userName',
        'email',
        'password',
        'uuid',
        'is_admin',
        'code',
        'email_verified_at',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function primaryLinks()
    {
        return $this->hasManyThrough(PrimaryLink::class, Profile::class);
    }

    public function links()
    {
        return $this->hasManyThrough(Link::class, Profile::class);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        //        'password' => 'hashed',
    ];
}
