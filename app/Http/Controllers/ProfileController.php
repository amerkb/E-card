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
use App\Models\Profile;
use App\Models\ProfilePrimaryLink;
use App\Models\Section;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProfileController extends Controller
{
    public function show(Profile $profile)
    {
        $p = Profile::with(['primary' => function ($query) {
            $query->where('available', true);
        }, 'links' => function ($query) {
            $query->where('available', true);
        }, 'sections' => function ($query) {
            $query->where('available', true);
        }])->find($profile->id);

        return new ProfileResource($p);
    }

    public function create_personal_data(ProfileRequest $request)
    {

        $profile = auth()->user()->profile()->create($request->validated());

        return $profile;

    }

    public function create_links(step2ProfileRequest $request)
    {
        $user = User::find(Auth::id());
        $profile = $user->profile;
        ProfilePrimaryLink::where('profile_id', $profile->id)->delete();
        Link::where('profile_id', $profile->id)->delete();
        Section::where('profile_id', $profile->id)->delete();

        if (isset($request->primaryLinks)) {
            $primaryLinks = [];
            foreach ($request->primaryLinks as $index => $primaryLink) {
                $primaryLinks[$index] = [
                    'profile_id' => $profile->id,
                    'primary_link_id' => $primaryLink['id'],
                    'value' => $primaryLink['value'],
                ];
            }
            ProfilePrimaryLink::insert($primaryLinks);
        }

        if (isset($request->secondLinks)) {
            $links = [];
            foreach ($request->secondLinks as $index => $link) {
                $links[$index] = [
                    'profile_id' => $profile->id,
                    'name_link' => $link['name_link'],
                    'link' => $link['link'],
                    'logo' => Link::store_logo($link['logo']),
                ];

            }
            Link::insert($links);
        }
        if (isset($request->sections)) {
            $sections = [];
            foreach ($request->sections as $index => $section) {
                $sections[$index] = [
                    'profile_id' => $profile->id,
                    'title' => $section['title'],
                    'name_of_file' => $section['name_of_file'],
                    'media' => Section::store_media($section['media']),
                ];
            }
            Section::insert($sections);
        }

        return response(['primary_links' => ProfilePrimaryLinkResource::collection($profile->primary),
            'second_links' => LinkResource::collection($profile->links),
            'section' => SectionResource::collection($profile->sections),
        ]);

    }

    public function create_other_data(step3ProfileRequest $request)
    {
        $user = User::find(Auth::id());
        $profile = $user->profile;
        $profile->update($request->validated());

        return response()->json(
            [
                'profile' => $profile,
            ],
            201);
    }

    public function update(EditProfileRequest $request, Profile $profile)
    {
        // abort_if($profile->user_id != auth()->user()->id , 403 ,'unauthorized');

        $profile->update($request->safe()->except('primaryLinks', 'secondLinks', 'sections'));

        if (isset($request->primaryLinks)) {
            $profile->primary()->detach();

            foreach ($request->primaryLinks as $primaryLink) {
                $profile->primary()->attach($primaryLink['id'], [
                    'value' => $primaryLink['value'],
                ]);
            }
        }
        if (isset($request->secondLinks)) {
            $profile->links()->delete();
            foreach ($request->secondLinks as $link) {
                Link::create([
                    'profile_id' => $profile->id,
                    'name_link' => $link['name_link'],
                    'link' => $link['link'],
                    'logo' => $link['logo'],
                ]);
            }

        }
        if (isset($request->sections)) {
            $profile->sections()->delete();
            foreach ($request->sections as $section) {
                Section::create([
                    'profile_id' => $profile->id,
                    'title' => $section['title'],
                    'name_of_file' => $section['name_of_file'],
                    'media' => $section['media'],
                ]);
            }

        }

        return response()->json(['data' => new ProfileResource($profile), 'message' => 'Data Saved Succcessfully']);
    }

    public function destroy(Profile $profile)
    {
        abort_if($profile->user_id != auth()->user()->id, 403, 'unauthorized');
        $profile->delete();

        return response()->json(['message' => 'Successfully Deleted Profile'], 200);
    }

    public function visitProfile(Profile $profile)
    {
        $profile->update(['views' => $profile->views + 1]);

        return $profile;
    }

    public function getViews_profile(Request $request)
    {

        $year = $request->year;
        $month = $request->month;
        $day = $request->day;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $profile = Profile::where('user_id', auth()->user()->id);

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

    public function visitPrimary(Profile $profile, $primaryLink)
    {
        $primaryLink = $profile->primary->where('id', $primaryLink)->first();
        if (! $primaryLink) {
            return response(['msg' => 'Primary link not found for this profile'], 404);
        }

        $ProfilePrimaryLink = ProfilePrimaryLink::where('primary_link_id', $primaryLink->id)
            ->where('profile_id', $profile->id)
            ->first();

        if ($ProfilePrimaryLink) {
            $ProfilePrimaryLink->update(['views' => $ProfilePrimaryLink->views + 1]);

            return $ProfilePrimaryLink;
        } else {
            // Handle the case where the ProfilePrimaryLink is not found
            return response(['msg' => 'ProfilePrimaryLink not found'], 404);
        }
    }

    public function changeAvailableP_Link(Profile $profile, ProfilePrimaryLink $profilePrimaryLink)
    {
        abort_if($profile->user_id != auth()->user()->id, 403, 'unauthorized');
        $profilePrimaryLink->update(['available' => ! $profilePrimaryLink->available]);

        return response()->json(['message' => 'update Available successfully']);
    }

    public function get_All_links(Profile $profile)
    {
        abort_if($profile->user_id != auth()->user()->id, 403, 'unauthorized');

        return new ShowLinksResource($profile->load(['links', 'primary']));
    }

    public function creates_profiles(Request $request)
    {

        $lastUser = User::MAX('id');
        $users = [];
        $password = [];

        // Perform the operations or code you want to measure

        for ($i = 0; $i < $request->number; $i++) {
            $password[$i] = rand(100000, 999999);
            $users[$i] = [
                'id' => $lastUser + 1 + $i,
                'password' => md5($password[$i]),
                'userName' => 'x tag'.$lastUser + 1 + $i,
                'uuid' => Str::uuid(),
                'is_admin' => false,
            ];

        }
        User::insert($users);

        //         Create a new Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'user_id');
        $sheet->setCellValue('B1', 'X tag');
        $sheet->setCellValue('C1', 'Password');
        $sheet->setCellValue('D1', 'uuid');
        $sheet->setCellValue('E1', 'link');

        $cellRange1 = 'A'. 1 .':E'. 1;
        $sheet->getStyle($cellRange1)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cellRange1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cellRange1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($cellRange1)->getAlignment()->setWrapText(true);
        $sheet->getStyle($cellRange1)->getAlignment()->setIndent(1);
        $sheet->getStyle($cellRange1)->getAlignment()->setShrinkToFit(true);

        // Populate user data
        foreach ($users as $index => $user) {

            $row = $index + 2;
            $sheet->setCellValue('A'.$row, $user['id']);
            $sheet->setCellValue('B'.$row, $user['userName']);
            $sheet->setCellValue('C'.$row, $password[$index]);
            $sheet->setCellValue('D'.$row, $user['uuid']);
            $sheet->setCellValue('E'.$row, 'https://user.x-tag.tech/?user='.$user['uuid']);

            // Set cell styling and spacing
            $cellRange = 'A'.$row.':E'.$row;
            $sheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cellRange)->getAlignment()->setWrapText(true);
            $sheet->getStyle($cellRange)->getAlignment()->setIndent(1);
            $sheet->getStyle($cellRange)->getAlignment()->setShrinkToFit(true);
        }

        // Set the file name and save the Excel file
        $fileName = 'users.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        // Return the file as a response
        return response()->download($fileName)->deleteFileAfterSend(true);

    }

    public function get_profiles(Request $request)
    {

        $users = User::with('profile')->where('id', '>=', $request->start)
            ->where('id', '<=', $request->end)
            ->get();
        //         Create a new Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'user_id');
        $sheet->setCellValue('B1', 'X tag');
        $sheet->setCellValue('C1', 'First Name');
        $sheet->setCellValue('D1', 'Last name ');
        $sheet->setCellValue('E1', 'job title');
        $sheet->setCellValue('F1', 'business name');
        $sheet->setCellValue('G1', 'location');
        $sheet->setCellValue('H1', 'bio');
        $sheet->setCellValue('I1', 'phone number');
        $sheet->setCellValue('J1', 'phone number secondary');
        $sheet->setCellValue('K1', 'email');
        $sheet->setCellValue('L1', 'uuid');
        $sheet->setCellValue('M1', 'link');

        $cellRange1 = 'A'. 1 .':M'. 1;
        $sheet->getStyle($cellRange1)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($cellRange1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cellRange1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($cellRange1)->getAlignment()->setWrapText(true);
        $sheet->getStyle($cellRange1)->getAlignment()->setIndent(1);
        $sheet->getStyle($cellRange1)->getAlignment()->setShrinkToFit(true);
        // Populate user data
        foreach ($users as $index => $user) {
            $row = $index + 2;
            $sheet->setCellValue('A'.$row, $user->id);
            $sheet->setCellValue('B'.$row, $user->userName);
            $sheet->setCellValue('C'.$row, $user->profile->firstName ?? 'NULL');
            $sheet->setCellValue('D'.$row, $user->profile->lastName ?? 'NULL');
            $sheet->setCellValue('E'.$row, $user->profile->jobTitle ?? 'NULL');
            $sheet->setCellValue('F'.$row, $user->profile->businessName ?? 'NULL');
            $sheet->setCellValue('G'.$row, $user->profile->location ?? 'NULL');
            $sheet->setCellValue('H'.$row, $user->profile->bio ?? 'NULL');
            $sheet->setCellValue('I'.$row, $user->profile->phoneNum ?? 'NULL');
            $sheet->setCellValue('J'.$row, $user->profile->phoneNumSecondary ?? 'NULL');
            $sheet->setCellValue('K'.$row, $user->profile->email ?? 'NULL');
            $sheet->setCellValue('L'.$row, $user->uuid);
            $sheet->setCellValue('M'.$row, 'https://user.x-tag.tech/?user='.$user->uuid);
            if (! empty($user->profile->primary)) {
                $leteer = 'M';
                foreach ($user->profile->primary as $primaryLink) {
                    $sheet->setCellValue(++$leteer.$row, $primaryLink->name);
                    $sheet->setCellValue(++$leteer.$row, $primaryLink->pivot->value);

                }
            }
            // Set cell styling and spacing
            $cellRange = 'A'.$row.':Z'.$row;
            $sheet->getStyle($cellRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($cellRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cellRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle($cellRange)->getAlignment()->setWrapText(true);
            $sheet->getStyle($cellRange)->getAlignment()->setIndent(1);
            $sheet->getStyle($cellRange)->getAlignment()->setShrinkToFit(true);

        }
        // Set the file name and save the Excel file
        $fileName = 'users.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        // Return the file as a response
        return response()->download($fileName)->deleteFileAfterSend(true);

    }

    public function update_profile_created_at(Request $request)
    {
        $user = Profile::find($request->profile_id);
        $user->created_at = Carbon::now();
        $user->save();
        return response()->json(['message' => 'Updated successfully']);
    }
    public function get_profiles_expiration()
    {
        $profiles = Profile::all();
        $filteredUsers = $profiles->filter(function ($user) {
           return Carbon::now() >=  Carbon::parse($user->created_at)->addYears(1);
        });
        return ProfileResource::collection($filteredUsers);
    }

}
