<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThemeRequest;
use App\Http\Requests\UpdateThemeRequest;
use App\Http\Resources\ThemeResource;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ThemeController extends Controller
{
    public function store(ThemeRequest $request) {
        $theme = Theme::create($request->validated());
        return response()->json(['message'=>'Added Successfully','data'=>$theme]);
    }
    public function update(UpdateThemeRequest $request , Theme $theme) {
        File::delete(public_path($theme->image));
        $theme->update($request->validated());
        return response()->json(['message'=>'Updated Successfully','data'=>$theme]);
    } 
    public function destroy(Theme $theme) {
        File::delete(public_path($theme->image));
        $theme->delete();
        return response()->json(['message'=>'Deletd Successfully']);
    } 
    public function index() {
        return ThemeResource::collection(Theme::all());
    }
    public function show(Theme $theme) {
        return ThemeResource::make($theme)
;    }
}
