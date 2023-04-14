<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login\LoginStandardRequest;
use App\Http\Requests\Auth\Login\LoginStateRequest;
use App\Http\Requests\Auth\Login\LogoutRequest;
use App\Http\Requests\Auth\Login\LoginSocialRequest;
use App\Models\User;
use App\Traits\AuthenticationTrait;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
  use AuthenticationTrait;

  private function isUserRegistered($provider, $id, $email)
  {
    if (!in_array($provider, User::PROVIDERS)) {
      return false;
    }

    $existsQuery = User::query();

    if (isset($email) && !empty($email)) {
      $existsQuery->orWhere('email', $email);
    }

    return $existsQuery->orWhere($provider . '_id', $id)->exists();
  }

  private function socialLogin(LoginSocialRequest $request, $provider)
  {
    $userSocialId = $request->post('user_id');
    $info = $request->post('info');

    $exists = $this->isUserRegistered($provider, $userSocialId, $info['email']);

    $providerField = $provider . '_id';

    if (!$exists) {
      $user = User::create([
        'role' => User::ROLE_STUDENT,
        'provider' => $provider,
        $providerField => $userSocialId,
        'first_name' => $info['first_name'],
        'last_name' => $info['last_name'],
        'email' => $info['email'],
        'avatar' => $info['avatar'],
        'verified_at' => date('Y-m-d H:i:s'),
      ]);
    } else {
      $user = User::query()
        ->where($providerField, $userSocialId)
        ->orWhere('email', $info['email'])
        ->first();

      $user->first_name = $info['first_name'];
      $user->last_name = $info['last_name'];
      if ($user->avatar == 'avatar.png') {
        $user->avatar = $info['avatar'];
      }

      if (is_null($user->email) || empty($user->email)) {
        $user->email = $info['email'];
      }
      if (is_null($user->$providerField) || empty($user->$providerField)) {
        $user->$providerField = $userSocialId;
      }

      $user->save();
    }

    return $this->authenticateUserWithToken($user);
  }

  /**
   * POST: /api/users/login
   */
  public function login(LoginStandardRequest $request)
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
   * GET: /api/users/login/state
   */
  public function checkIsRegistered(LoginStateRequest $request)
  {
    $exists = $this->isUserRegistered(
      $request->query('provider'),
      $request->query('id'),
      $request->query('email')
    );

    return response()->json([
      'exists' => $exists,
    ]);
  }

  /**
   * POST: /api/users/logout
   */
  public function logout(LogoutRequest $request)
  {
    $user = $request->user();
    $this->forgetUser($user);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * POST: /api/users/facebook/:id
   */
  public function facebookLogin(LoginSocialRequest $request, $id)
  {
    return $this->socialLogin($request, User::PROVIDER_FACEBOOK);
  }

  /**
   * POST: /api/users/google/:id
   */
  public function googleLogin(LoginSocialRequest $request, $id)
  {
    return $this->socialLogin($request, User::PROVIDER_GOOGLE);
  }

  /**
   * POST: /api/users/apple/:id
   */
  public function appleLogin(LoginSocialRequest $request, $id)
  {
    return $this->socialLogin($request, User::PROVIDER_APPLE);
  }
}
