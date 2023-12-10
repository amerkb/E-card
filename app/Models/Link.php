<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function profile():BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function setLogoAttribute($logo) {
        $newLogoName = uniqid() . '_' . 'logo' . '.' . $logo->extension();
        $logo->move(public_path('/Extralinks/logo/'), $newLogoName);
        return $this->attributes['logo'] = '/Extralinks/logo/' . $newLogoName;
    }
}
