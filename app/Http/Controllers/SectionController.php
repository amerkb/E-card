<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Profile;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function changeAvailable (Profile $profile , Section $section) {
        abort_if(auth()->user()->id != $profile->user_id && $profile->id != $section->profile_id,403,'unauthorized');
        $section->update(['available' => ! $section->available]);
        return response()->json(['message' => 'update Available successfully']);
    }
    public function getSections(Profile $profile) {
        return SectionResource::collection($profile->sections()->get());
    }
    public function AddSection(Profile $profile ,AddSectionRequest $request) {
        $section = $profile->sections()->create($request->validated());
        return response()->json(['message' => 'Added successfully' , 'section' => $section]);
    }
    public function UpdateSection(Profile $profile , Section $section , UpdateSectionRequest $request) {
        abort_if(auth()->user()->id != $profile->user_id && $profile->id != $section->profile_id,403,'unauthorized');
        $section->update($request->validated());
        return response()->json(['message' => 'Updated successfully' , 'section' => $section]);
    }
    public function DeleteSection(Profile $profile , Section $section) {
        abort_if(auth()->user()->id != $profile->user_id && $profile->id != $section->profile_id,403,'unauthorized');
        $section->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
