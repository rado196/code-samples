<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPassword\CreateResetPasswordRequest;
use App\Http\Requests\Auth\ResetPassword\UpdatePasswordRequest;
use App\Http\Requests\Auth\ResetPassword\ValidateTokenRequest;
use App\Models\PasswordReset;
use App\Models\User;
use App\Traits\AuthenticationTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
  use AuthenticationTrait;

  /**
   * POST: /api/users/password/recovery
   */
  public function create(CreateResetPasswordRequest $request)
  {
    $user = User::query()
      ->where('email', $request->email)
      ->first();

    return $this->withStandardUser($user, function (User $user) {
      PasswordReset::query()
        ->where('user_id', $user->id)
        ->delete();

      $passwordReset = new PasswordReset();
      $passwordReset->user_id = $user->id;
      $passwordReset->save();

      return response()->json([
        'status' => 'success',
      ]);
    });
  }

  /**
   * GET: /api/users/password/recovery
   */
  public function find(ValidateTokenRequest $request)
  {
    $passwordReset = PasswordReset::query()
      ->where('reset_token', $request->get('reset_token'))
      ->first();

    if (is_null($passwordReset)) {
      return response()->json([
        'status' => 'failure',
        'message' => 'auth_error_invalid_reset_token',
      ]);
    }

    $isPast = Carbon::parse($passwordReset->updated_at)
      ->addMinutes(1)
      ->isPast();

    if ($isPast) {
      $passwordReset->delete();
      return response()->json([
        'status' => 'failure',
        'message' => 'auth_error_invalid_reset_token',
      ]);
    }

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * PUT: /api/users/password/recovery
   */
  public function reset(UpdatePasswordRequest $request)
  {
    $passwordReset = PasswordReset::query()
      ->where('reset_token', $request->get('reset_token'))
      ->with('user')
      ->first();

    if (is_null($passwordReset)) {
      return response()->json([
        'status' => 'failure',
        'message' => 'auth_error_invalid_reset_token',
      ]);
    }

    $user = $passwordReset->user;
    if (is_null($user)) {
      return response()->json([
        'status' => 'failure',
        'message' => 'auth_error_reset_email',
      ]);
    }

    $user->password = Hash::make($request->post('password'));
    $user->save();

    $passwordReset->delete();
    return response()->json([
      'status' => 'success',
    ]);
  }
}
