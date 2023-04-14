<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\User\GetMeRequest;
use App\Traits\AuthenticationTrait;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  use AuthenticationTrait;

  public function getMe(GetMeRequest $request)
  {
    return $this->mergeLoginData(Auth::user());
  }
}
