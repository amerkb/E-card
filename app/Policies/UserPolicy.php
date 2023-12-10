<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function update(User $user) {
        return $user->id === auth()->user()->id;
    }
}
