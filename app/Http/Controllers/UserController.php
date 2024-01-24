<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditUserRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function show(User $user)
    {
        return ProfileResource::make($user->profile);

    }

    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function store(RegisterRequest $request)
    {

        $request->validated();

        $user = User::create(array_merge($request->except('password'),
            ['password' => md5($request->password)]
        ));

        return response(['user' => UserResource::make($user)]);

    }

    public function update(EditUserRequest $request, User $user)
    {
        $this->authorize('update', [User::class, $user]);
        $user->update($request->validated());
        $user->sendEmailVerificationNotification();

        return response(['message' => 'check Your Email To verfiy']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Successfully Deleted'], 200);
    }
}
