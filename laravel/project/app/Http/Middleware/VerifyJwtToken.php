<?php

namespace App\Http\Middleware;

use App\Traits\AuthenticationTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PeterPetrus\Auth\PassportToken;

class VerifyJwtToken
{
  use AuthenticationTrait;

  private function checkNeedToLogout($accessToken)
  {
    if (is_null($accessToken)) {
      return Auth::check();
    }

    if (Auth::guest()) {
      return true;
    }

    $personalToken = new PassportToken($accessToken);
    if (!$personalToken->valid) {
      return true;
    }

    if (!$personalToken->existsValid()) {
      return true;
    }

    if ($personalToken->expires_at_unix - time() <= 0) {
      return true;
    }

    return false;
  }

  private function parseToken($accessToken)
  {
    if (Str::startsWith($accessToken, 'Bearer ')) {
      return Str::substr($accessToken, 7);
    }

    return $accessToken;
  }

  /**
   * Handle an incoming request.
   *
   * @param Request $request
   * @param Closure $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    return $next($request);

    // $accessToken = $request->header('Authorization', '');
    // $needToLogout = true;

    // if ('' != $accessToken) {
    //   $accessToken = $this->parseToken($accessToken);
    //   $needToLogout = $this->checkNeedToLogout($accessToken);
    // }

    // return $this->verify($request, $next, $needToLogout);
  }
}
