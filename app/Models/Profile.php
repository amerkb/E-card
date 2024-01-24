<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class);
    }

    public function primary(): BelongsToMany
    {
        return $this->belongsToMany(PrimaryLink::class, 'profile_primary_links')->withPivot('value', 'views', 'available');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function setCoverAttribute($cover)
    {
        if ($cover == null || $cover == 'NULL' || $cover == 'null' || $cover == 'Null') {
            return $this->attributes['cover'] = null;
        } else {
            $newCoverName = uniqid().'_'.'cover'.'.'.$cover->extension();
            $cover->move(public_path('images/user/'), $newCoverName);

            return $this->attributes['cover'] = '/images/user/'.$newCoverName;
        }
    }

    public function setPhotoAttribute($photo)
    {
        if ($photo == null || $photo == 'NULL' || $photo == 'null' || $photo == 'Null') {
            return $this->attributes['photo'] = null;
        } else {
            $newPhotoName = uniqid().'_'.'photo'.'.'.$photo->extension();
            $photo->move(public_path('images/user/'), $newPhotoName);

            return $this->attributes['photo'] = '/images/user/'.$newPhotoName;
        }
    }
}
