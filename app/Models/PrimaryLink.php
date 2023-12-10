<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PrimaryLink extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function profiles()
    {
        return $this->belongsToMany(Profile::class,'profile_primary_links');
    }
    public function setLogoAttribute($logo) {
        $newLogoName = uniqid() . '_' . 'logo' . '.' . $logo->extension();
        $logo->move(public_path('/PrimaryLinks/logo/'), $newLogoName);
        return $this->attributes['logo'] = '/PrimaryLinks/logo/' . $newLogoName;
    }
}
