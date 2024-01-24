<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\NewPasswordController;
use App\Http\Controllers\PrimaryLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyEmailController;
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
Route::get('/profile/{profile}', [ProfileController::class, 'show']);
Route::post('/checkforemail', [AuthController::class, 'check']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/create_code', [AuthController::class, 'create_code']);
Route::post('/check_code', [AuthController::class, 'check_code']);
Route::post('logout', [AuthController::class, 'logout']);
Route::get('/user/{user:uuid}', [UserController::class, 'show']);
Route::post('/change_password', [AuthController::class, 'change_password']);
Route::post('forgotPassword', [NewPasswordController::class, 'forgotPassword'])->name('password.email');
Route::post('password/reset', [NewPasswordController::class, 'passwordReset'])->name('password.reset');
Route::get('get_links_with_visit', [LinkController::class, 'get_links_with_visit']);
Route::post('{profile}/visit', [ProfileController::class, 'visitProfile']);
Route::post('link/{link}/visit', [LinkController::class, 'visitLink']);
Route::post('/{profile}/primary_link/{PrimaryLink}/visit', [ProfileController::class, 'visitPrimary']);
Route::get('P_link', [PrimaryLinkController::class, 'index']);
        Route::put('update_profile_created_at', [ProfileController::class, 'update_profile_created_at']);
Route::get('get_profiles_expiration', [ProfileController::class, 'get_profiles_expiration']);


Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('isAdmin')->group(function () {
        Route::get('get_value_for_Link', [GenerationController::class, 'show']);
        Route::post('generation', [GenerationController::class, 'store']);
        Route::put('create_value', [GenerationController::class, 'create_value']);
        Route::get('generation', [GenerationController::class, 'index']);
        Route::post('creates_profiles', [ProfileController::class, 'creates_profiles']);
        Route::get('get_profiles', [ProfileController::class, 'get_profiles']);
        Route::resource('user', UserController::class)->only('index', 'store', 'destroy');
        Route::patch('user/{user}', [UserController::class, 'update']);
        Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])->name('verification.verify');
        Route::get('theme', [ThemeController::class, 'index'])->withoutMiddleware(['isAdmin']);
        Route::get('theme/{theme}', [ThemeController::class, 'show'])->withoutMiddleware(['isAdmin']);
        Route::resource('theme', ThemeController::class)->only('store', 'destroy');
        Route::post('theme/{theme}', [ThemeController::class, 'update']);
        //        Route::get('P_link', [PrimaryLinkController::class, 'index'])->withoutMiddleware(['isAdmin']);
        Route::post('P_link', [PrimaryLinkController::class, 'store']);
        Route::post('P_link/{primaryLink}', [PrimaryLinkController::class, 'update']);
        Route::delete('P_link/{primaryLink}', [PrimaryLinkController::class, 'destroy']);
    });
    Route::prefix('profile')->group(function () {
        Route::post('change_email', [AuthController::class, 'change_email']);
        Route::post('/create_personal_data', [ProfileController::class, 'create_personal_data']);
        Route::post('/create_links', [ProfileController::class, 'create_links']);
        Route::post('/create_other_data', [ProfileController::class, 'create_other_data']);

        Route::post('/{profile}', [ProfileController::class, 'update']);
        Route::delete('/{profile}/primary_link/{profilePrimaryLink}', [PrimaryLinkController::class, 'DeletePrimaryLink']);
        // Route::post('/{profile}/primary_link/{profilePrimaryLink}',[PrimaryLinkController::class,'UpdatePrimaryLink']);
        // Route::post('/{profile}/primary_link',[PrimaryLinkController::class,'AddPrimaryLink']);
        Route::patch('/{profile}/primary_link/{profilePrimaryLink}/changeAvailable', [ProfileController::class, 'changeAvailableP_Link']);
        //        Route::post('{profile}/visit', [ProfileController::class, 'visitProfile']);
        Route::post('/{profile}/views', [ProfileController::class, 'getViews_profile']);
        //        Route::post('/{profile}/primary_link/{PrimaryLink}/visit', [ProfileController::class, 'visitPrimary']);
        Route::get('/{profile}/allLinks', [ProfileController::class, 'get_All_links']);

        //        Route::post('link/{link}/visit', [LinkController::class, 'visitLink']);
        Route::prefix('{profile}/link')->group(function () {
            Route::delete('/{link}', [LinkController::class, 'DeleteLink']);
            Route::post('/', [LinkController::class, 'AddLink']);
            Route::patch('/{link}/changeAvailable', [LinkController::class, 'changeAvailable']);
            Route::post('/{link}/views', [LinkController::class, 'getViews_link']);
        });

        Route::prefix('{profile}/section')->group(function () {
            Route::delete('/{section}', [SectionController::class, 'DeleteSection']);
            Route::post('/', [SectionController::class, 'AddSection']);
            Route::patch('/{section}/changeAvailable', [SectionController::class, 'changeAvailable']);
        });

    });
});
