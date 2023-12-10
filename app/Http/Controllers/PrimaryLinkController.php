<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddPrimaryLinkRequest;
use App\Http\Requests\StorePrimaryLinkRequest;
use App\Http\Requests\UpdatePrimaryLinkRequest;
use App\Http\Resources\PrimaryLinkResource;
use App\Http\Resources\ProfilePrimaryLinkResource;
use App\Models\PrimaryLink;
use App\Models\Profile;
use App\Models\ProfilePrimaryLink;
use Illuminate\Http\Request;

class PrimaryLinkController extends Controller
{
    public function store(AddPrimaryLinkRequest $request) {
        $primaryLink = PrimaryLink::create($request->validated());
        return response()->json(['message'=> 'Added Successfully' , 'data' => $primaryLink]);
    }
    public function update(UpdatePrimaryLinkRequest $request,PrimaryLink $primaryLink) {
        $primaryLink->update($request->validated());
        return response()->json(['message'=> 'updated Successfully' , 'data' => $primaryLink]);
    }
    public function destroy(PrimaryLink $primaryLink) {
        $primaryLink->delete();
        return response()->json(['message'=> 'deleted Successfully']);
    }
    public function index() {
        return PrimaryLinkResource::collection(PrimaryLink::all());
    }
    public function getPrimaryLinks(Profile $profile) {
        return ProfilePrimaryLinkResource::collection($profile->primary()->get());
    }
    public function DeletePrimaryLink(Profile $profile ,ProfilePrimaryLink $profilePrimaryLink) {
        $profilePrimaryLink->delete();
        return response()->json(['message'=> 'deleted Successfully']);
    }


}
