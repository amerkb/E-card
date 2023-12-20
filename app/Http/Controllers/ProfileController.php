<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditProfileRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\step2ProfileRequest;
use App\Http\Requests\step3ProfileRequest;
use App\Http\Resources\LinkResource;
use App\Http\Resources\ProfilePrimaryLinkResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\ShowLinksResource;
use App\Models\Link;
use App\Models\PrimaryLink;
use App\Models\Profile;
use App\Models\ProfilePrimaryLink;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    public function show(Profile $profile) {
        $p = Profile::with(['primary' => function ($query) {
            $query->where('available', true);
        },'links' => function ($query) {
            $query->where('available', true);
         }, 'sections' => function ($query) {
            $query->where('available', true);
         } ])->find($profile->id);
         return new ProfileResource($p);
    }

    public function create_personal_data(ProfileRequest $request) {

        $profile = auth()->user()->profile()->create($request->validated());
        return $profile;
//        return response()->json(['data' => new ProfileResource($profile) , 'message' => 'Data Saved Succcessfully']);

    }
    public function create_links(step2ProfileRequest $request)
    {
          $user= User::find(Auth::id());
          $profile=$user->profile;
        if (isset($request->primaryLinks)) {
            foreach($request->primaryLinks as $primaryLink) {
               ProfilePrimaryLink::create([
                'profile_id' => $profile->id,
                'primary_link_id' => $primaryLink['id'],
                'value' => $primaryLink['value'] ,
               ]);
            }
        }

        if (isset($request->secondLinks)) {
            foreach($request->secondLinks as $link) {
                Link::create([
                    'profile_id' => $profile->id,
                    'name_link' => $link['name_link'],
                    'link' => $link['link'],
                    'logo' => $link['logo']
                ]);
            }
        }
        if (isset($request->sections)) {
            foreach($request->sections as $section) {
                Section::create([
                    'profile_id' => $profile->id,
                    'title' => $section['title'],
                    'name_of_file' => $section['name_of_file'],
                    'media' => $section['media']
                ]);
            }
        }
        return response(     [       'primary_links' => ProfilePrimaryLinkResource::collection($profile->primary),
            'second_links' => LinkResource::collection($profile->links),
            'section' => SectionResource::collection($profile->sections),
        ]);
    }

    public function create_other_data(step3ProfileRequest $request){
        $user= User::find(Auth::id());
        $profile=$user->profile;
        $profile->update($request->validated());
        return response()->json(
            [
               "profile" => $profile
            ]
            ,
            201);
    }
        public function update(EditProfileRequest $request ,Profile $profile) {
        // abort_if($profile->user_id != auth()->user()->id , 403 ,'unauthorized');

            $profile->update($request->safe()->except('primaryLinks','secondLinks','sections'));

        if (isset($request->primaryLinks)) {
            $profile->primary()->detach();
            foreach($request->primaryLinks as $primaryLink) {
                $profile->primary()->attach($primaryLink['id'], [
                    'value' => $primaryLink['value']
                ]);
            }


        }
        if (isset($request->secondLinks)) {
           $profile->links()->delete();
           foreach($request->secondLinks as $link) {
            Link::create([
                'profile_id' => $profile->id,
                'name_link' => $link['name_link'],
                'link' => $link['link'],
                'logo' => $link['logo']
            ]);
           }

        }
        if (isset($request->sections)) {
            $profile->sections()->delete();
            foreach($request->sections as $section) {
                Section::create([
                    'profile_id' => $profile->id,
                    'title' => $section['title'],
                    'name_of_file' => $section['name_of_file'],
                    'media' => $section['media']
                ]);
            }


        }

        return response()->json(['data' => new ProfileResource($profile),'message' => 'Data Saved Succcessfully']);
    }
    public function destroy(Profile $profile) {
        abort_if($profile->user_id != auth()->user()->id , 403 ,'unauthorized');
        $profile->delete();
        return response()->json(['message' => 'Successfully Deleted Profile'] ,200);
    }

    public function visitProfile(Profile $profile) {
        $profile->update(['views' => $profile->views +1]);
        return $profile;
    }
    public function getViews_profile(Request $request) {

        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $profile = Profile::where('user_id',auth()->user()->id);

        if ($year && $month && $day) {
            $profile->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day)->select('views');
        } elseif ($year && $month) {
            $profile->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)->select('views');
        } elseif ($year) {
            $profile->whereYear('created_at', $year)->select('views');
        } elseif ($day) {
            $profile->whereDay('created_at', $day)->select('views');
        } elseif ($startDate && $endDate) {
            $profile->whereBetween('created_at', [$startDate, $endDate])->select('views');
        }
        $data = $profile->first();
        if (! $data) {
            return response(['views' => 0]);
        }
        return response(['views' => $data->views]);
    }
    public function visitPrimary(Profile $profile , ProfilePrimaryLink $profilePrimaryLink) {
        $profilePrimaryLink->update(['views' => $profilePrimaryLink->views +1]);
        return $profilePrimaryLink;
    }
    public function changeAvailableP_Link(Profile $profile , ProfilePrimaryLink $profilePrimaryLink) {
        abort_if($profile->user_id != auth()->user()->id , 403 ,'unauthorized');
        $profilePrimaryLink->update(['available' => ! $profilePrimaryLink->available]);
        return response()->json(['message' => 'update Available successfully']);
    }
    public function get_All_links(Profile $profile) {
        abort_if($profile->user_id != auth()->user()->id , 403 ,'unauthorized');
       return new ShowLinksResource($profile->load(['links','primary']));
    }



}
