<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function setLogoAttribute($logo)
    {
        if (is_string($logo)) {
            return $this->attributes['logo'] = $logo;
        } else {
            $newLogoName = uniqid().'_'.'logo'.'.'.$logo->extension();
            $logo->move(public_path('/Extralinks/logo/'), $newLogoName);

            return $this->attributes['logo'] = '/Extralinks/logo/'.$newLogoName;
        }
    }

    public static function store_logo($logo)
    {
        if (is_string($logo)) {
            return $logo;
        } else {
            $newLogoName = uniqid().'_'.'logo'.'.'.$logo->extension();
            $logo->move(public_path('/Extralinks/logo/'), $newLogoName);

            return '/Extralinks/logo/'.$newLogoName;
        }
    }
}
