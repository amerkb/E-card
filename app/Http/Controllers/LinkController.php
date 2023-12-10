<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Models\Profile;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function getLinks(Profile $profile) {
        abort_if(auth()->user()->id != $profile->user_id , 403 , 'unauthorized');
        return LinkResource::collection($profile->links()->get());
    }
    public function DeleteLink(Profile $profile , Link $link) {
        abort_if(auth()->user()->id != $profile->user_id , 403 , 'unauthorized');
        $link->delete();
        return response()->json(['message' => 'Done']);
    }
    public function UpdateLink(Profile $profile , Link $link , UpdateLinkRequest $request) {
        abort_if($profile->user_id != auth()->user()->id , 403 ,'unauthorized');
        $link->update($request->validated() ,['profile_id' => $profile->id]);
        return response()->json(['data' => new LinkResource($link),'message' => 'Data Saved Succcessfully']);

    }
    public function AddLink(Profile $profile ,AddLinkRequest $request) {
        $link = Link::create(array_merge($request->validated() ,['profile_id' => $profile->id]));
        return response()->json(['data' => new LinkResource($link),'message' => 'Data Saved Succcessfully']);

    }
    public function visitLink(Profile $profile,Link $link) {
        $link->update(['views' => $link->views +1]);
        return $link;
    }
    public function changeAvailable (Profile $profile ,Link $link) {
        abort_if(auth()->user()->id != $profile->user_id && $profile->id != $link->profile_id,403,'unauthorized');
        $link->update(['available' => ! $link->available]);
        return response()->json(['message' => 'update Available successfully']);
    }
    public function getViews_link(Request $request,Profile $profile, Link $link) {
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $query = Link::where('id',$link->id);

        if ($year && $month && $day) {
            $query->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereDay('created_at', $day);
        } elseif ($year && $month) {
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } elseif ($year) {
            $query->whereYear('created_at', $year);
        } elseif ($day) {
            $query->whereDay('created_at', $day);
        } elseif ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $data = $query->first();
       
        return response(['data' => isset($data) ? ['name'=> $data->name_link, 'link' => $data->link, 'views' => $data->views] : 0]);
    }
}
