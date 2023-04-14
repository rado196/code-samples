<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait AuthenticationTrait
{
  /**
   * Forgot user and revoke token.
   */
  private function forgetUser(User $user = null)
  {
    if (is_null($user)) {
      return;
    }

    $token = $user->currentAccessToken();
    if (!is_null($token)) {
      $user
        ->tokens()
        ->where('id', $token->id)
        ->delete();
    }
  }

  /**
   * Verify authentication.
   */
  private function verify(
    Request $request,
    Closure $next,
    $needToLogout = false
  ) {
    if ($needToLogout) {
      $user = Auth::user();
      $this->forgetUser($user);

      $response = new JsonResponse([
        'auth_remove_token' => true,
      ]);
    } else {
      $response = $next($request);
    }

    return $response;
  }

  /**
   * Merge login data to user data.
   */
  private function mergeLoginData($user, $data = [])
  {
    $userWallet = Wallet::query()
      ->where('user_id', Auth::id())
      ->first();

    $data = array_merge($data, [
      'user' => [
        'id' => $user->id,
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'role' => $user->role,
        'avatar' => $user->avatar,
        'email' => $user->email,
        'phone' => $user->phone,
        'gender' => $user->gender,
        'verified_at' => $user->verified_at,
        'created_at' => $user->created_at,
      ],
      'wallet' => [
        'id' => $userWallet->id,
        'balance' => $userWallet->balance,
      ],
    ]);

    return response()->json($data);
  }

  /**
   * Authenticate user with token.
   */
  private function authenticateUserWithToken(User $user)
  {
    Auth::guard('api')->setUser($user, true);
    $tokenResult = $user->createToken('Personal Access Token');

    return $this->mergeLoginData($user, [
      'status' => 'success',
      'access_token' => $tokenResult->plainTextToken,
    ]);
  }

  /**
   * User which registered using social media provider cannot have a password .
   */
  private function withStandardUser(?Authenticatable $user, callable $callback)
  {
    if (is_null($user)) {
      return response()->json([
        'status' => 'failure',
        'message' => 'invalid_user',
      ]);
    }

    if ($user->provider !== User::PROVIDER_STANDARD) {
      return response()->json([
        'status' => 'failure',
        'message' => 'auth_error_not_a_standard_user',
        'provider' => $user->provider,
      ]);
    }

    return $callback($user);
  }
}
