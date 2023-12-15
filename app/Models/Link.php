<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class Link extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function profile():BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function setLogoAttribute($logo) {
        if (Str::startsWith($logo, 'data:image')){
            return $this->attributes['logo']= $this->store_image($logo);
        }
        else {
            return $this->attributes['logo'] =$logo;
        }

    }
    public function store_image($request){
        $decodedImage =$request;
        $base64String = substr($decodedImage, strpos($decodedImage, ',') + 1);
        $decodedImage = base64_decode($base64String);
        $fileName = "logo".time().'.jpg';
        $filePath = 'images/'.$fileName;
        Storage::disk('uploads')->put($filePath, $decodedImage);
        return "uploads/images/".$fileName;
    }
}
