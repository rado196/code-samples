<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AuthenticationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
  use AuthenticationTrait;

  /**
   * POST: /api/users/login
   */
  public function login(Request $request)
  {
    $user = User::query()
      ->where('email', $request->post('email'))
      ->first();

    $isValidLogin = !is_null($user);

    if ($isValidLogin) {
      $passwordsMatch = Hash::check(
        $request->post('password'),
        $user->password
      );
      if (!$passwordsMatch) {
        $isValidLogin = false;
      }
    }

    if (!$isValidLogin) {
      return response()->json(
        [
          'status' => 'failure',
          'errors' => [
            'email' => ['auth_error_incorrect_email_or_password'],
          ],
        ],
        401
      );
    }

    if (is_null($user->verified_at)) {
      return response()->json(
        [
          'status' => 'failure',
          'errors' => [
            'email' => ['auth_error_email_not_verified'],
          ],
        ],
        401
      );
    }

    return $this->authenticateUserWithToken($user);
  }

  /**
   * POST: /api/admin/logout
   */
  public function logout(Request $request)
  {
    $user = $request->user();
    $this->forgetUser($user);

    return response()->json([
      'status' => 'success',
    ]);
  }
}
