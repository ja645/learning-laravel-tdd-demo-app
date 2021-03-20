<?php

namespace Tests\Factories\Traits;

use App\Models\User;
use App\Models\UserProfile;

trait CreatesUser
{
  private function createUser(): User
  {
    $user = \App\Models\User::factory()->create();
    $user->profile()->save(\App\Models\UserProfile::factory()->make());

    return $user;
  }
}