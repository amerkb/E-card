<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Section extends Model
{
    use HasFactory;
    protected $fillable = [
        'profile_id' , 'title' , 'name_of_file' , 'media' ,'available'
    ];
    public function profile():BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
    public function setMediaAttribute($media) {
        if (Str::startsWith($media, 'data:image')){
            return $this->attributes['media'] = $this->store_image($media);
        }
        else {
            return $this->attributes['media'] = $media;
        }

    }
    public function store_image($request){
        $decodedImage =$request;
        $base64String = substr($decodedImage, strpos($decodedImage, ',') + 1);
        $decodedImage = base64_decode($base64String);
        $fileName = "media".time().'.jpg';
        $filePath = 'images/'.$fileName;
        Storage::disk('uploads')->put($filePath, $decodedImage);
        return "uploads/images/".$fileName;
    }
}
