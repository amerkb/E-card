<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;
    protected $fillable = ['image'];

    public function setImageAttribute($image) {
        $newImageName = uniqid() . '_' . 'image' . '.' . $image->extension();
        $image->move(public_path('themes/'), $newImageName);
        return $this->attributes['image'] = '/themes/' . $newImageName;
    }
}
