<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePrimaryLink extends Model
{
    use HasFactory;
    protected $fillable = ['profile_id' , 'primary_link_id' ,'views','value','available'];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function primaryLink()
    {
        return $this->belongsTo(PrimaryLink::class);
    }
}
