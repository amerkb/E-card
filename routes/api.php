<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PrimaryLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::get('/profile/{profile}', [ProfileController::class, 'show']);
Route::get('/user/{user:uuid}', [UserController::class, 'show']);
Route::get('/check_for_email', [AuthController::class, 'check']);
Route::post('/create_code', [AuthController::class, 'create_code']);
Route::post('/check_code', [AuthController::class, 'check_code']);
Route::post('/change_password', [AuthController::class, 'change_password']);
Route::get('P_link', [PrimaryLinkController::class, 'index']);
Route::post('profile/{profile}/visit', [ProfileController::class, 'visitProfile']);
Route::post('link/{link}/visit', [LinkController::class, 'visitLink']);
Route::post('/{profile}/primary_link/{PrimaryLink}/visit', [ProfileController::class, 'visitPrimary']);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('isAdmin')->group(function () {
        Route::put('update_profile_created_at', [ProfileController::class, 'update_profile_created_at']);
        Route::get('get_profiles_expiration', [ProfileController::class, 'get_profiles_expiration']);
        Route::get('get_value_for_Link', [GenerationController::class, 'show']);
        Route::post('generation', [GenerationController::class, 'store']);
        Route::put('create_value', [GenerationController::class, 'create_value']);
        Route::post('creates_profiles', [ProfileController::class, 'creates_profiles']);
        Route::get('get_profiles', [ProfileController::class, 'get_profiles']);
        Route::get('get_profile_by_id', [ProfileController::class, 'get_profile_by_id']);
        Route::get('get_profile_by_phone', [ProfileController::class, 'get_profile_by_phone']);
        Route::resource('user', UserController::class)->only('index', 'store', 'destroy');
        Route::patch('user/{user}', [UserController::class, 'update']);
        Route::get('theme', [ThemeController::class, 'index'])->withoutMiddleware(['isAdmin']);
        Route::get('theme/{theme}', [ThemeController::class, 'show'])->withoutMiddleware(['isAdmin']);
        Route::resource('theme', ThemeController::class)->only('store', 'destroy');
        Route::post('theme/{theme}', [ThemeController::class, 'update']);
        Route::post('P_link', [PrimaryLinkController::class, 'store']);
        Route::post('P_link/{primaryLink}', [PrimaryLinkController::class, 'update']);
        Route::delete('P_link/{primaryLink}', [PrimaryLinkController::class, 'destroy']);
    });
    Route::get('get_links_with_visit', [LinkController::class, 'get_links_with_visit']);
    Route::put('change_email', [AuthController::class, 'change_email']);

//    profile
    Route::prefix('profile')->group(function () {
        Route::post('/create_personal_data', [ProfileController::class, 'create_personal_data']);
        Route::post('/create_links', [ProfileController::class, 'create_links']);
        Route::post('/create_other_data', [ProfileController::class, 'create_other_data']);
        Route::post('/{profile}', [ProfileController::class, 'update']);
        Route::put('update/theme/{profile}', [ProfileController::class, 'updateTheme']);

    });
});
