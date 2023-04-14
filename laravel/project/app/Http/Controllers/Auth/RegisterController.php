<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Register\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
  /**
   * POST: /api/users
   */
  public function register(RegisterRequest $request)
  {
    $user = User::query()
      ->where('email', $request->email)
      ->first();

    if (is_null($user)) {
      $user = new User();

      $user->provider = User::PROVIDER_STANDARD;
      $user->role = User::ROLE_STUDENT;
      $user->email = $request->email;
    }

    $user->first_name = $request->first_name;
    $user->last_name = $request->last_name;
    $user->gender = $request->gender;
    $user->phone = $request->phone;
    $user->password = Hash::make($request->password);

    $user->save();

    return response()->json(
      [
        'status' => 'success',
        'message' => 'successfully_send_verification_message',
      ],
      201
    );
  }
}
