<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id', 'title', 'name_of_file', 'media', 'available',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function setMediaAttribute($media)
    {
        if (is_string($media)) {
            return $this->attributes['media'] = $media;
        } else {
            $newMediaName = uniqid().'_'.'media'.'.'.$media->extension();
            $media->move(public_path('media/user'), $newMediaName);

            return $this->attributes['media'] = '/media/user/'.$newMediaName;
        }
    }

    public static function store_media($media)
    {
        if (is_string($media)) {
            return $media;
        } else {
            $newMediaName = uniqid().'_'.'media'.'.'.$media->extension();
            $media->move(public_path('media/user'), $newMediaName);

            return '/media/user/'.$newMediaName;
        }
    }
}
