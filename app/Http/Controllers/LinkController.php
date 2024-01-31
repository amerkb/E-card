<?php

namespace App\Http\Controllers;

use App\Http\Resources\viewLinkResource;
use App\Http\Resources\viewPrimaryLinkResource;
use App\Models\Link;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function visitLink(Profile $profile, Link $link)
    {
        $link->update(['views' => $link->views + 1]);

        return $link;
    }

    public function get_links_with_visit()
    {
        $user = User::find(Auth::id());
        $primaryLinks = viewPrimaryLinkResource::collection($user->profile->primary);
        $links = viewLinkResource::collection($user->profile->links);

        return $mergedLinks = $primaryLinks->concat($links);
    }
}
