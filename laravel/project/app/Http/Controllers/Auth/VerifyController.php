<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmail\VerifyEmailRequest;
use App\Models\Language;
use App\Models\VerifyEmail;
use Carbon\Carbon;

class VerifyController extends Controller
{
  /**
   * POST: /api/users/verification
   */
  // public function verify(VerifyEmailRequest $request)
  // {
  //   $verifyEmail = VerifyEmail::query()
  //     ->where('verify_token', $request->post('verify_token'))
  //     ->with('user')
  //     ->first();

  //   if (is_null($verifyEmail)) {
  //     return response()->json([
  //       'message' => 'auth_error_reset_token_invalid',
  //     ]);
  //   }

  //   $user = $verifyEmail->user;
  //   if (is_null($user)) {
  //     return response()->json([
  //       'message' => 'auth_error_reset_email',
  //     ]);
  //   }

  //   $user->verified_at = Carbon::now();
  //   $user->save();

  //   $verifyEmail->delete();
  //   return response()->json([
  //     'status' => 'success',
  //   ]);
  // }

  public function verify(VerifyEmailRequest $request)
  {
    $status = null;

    $verifyEmail = VerifyEmail::query()
      ->where('verify_token', $request->verify_token)
      ->with('user')
      ->first();

    if (is_null($verifyEmail)) {
      $status = 'failure';
    }

    if (is_null($status)) {
      $user = $verifyEmail->user;
      if (is_null($user)) {
        $status = 'failure';
      }
    }

    if (is_null($status)) {
      $user->verified_at = Carbon::now();
      $user->save();

      $verifyEmail->delete();

      $status = 'success';
    }

    $appUrl = config('custom.web_app_url');
    if (str_ends_with($appUrl, '/')) {
      $appUrl = substr($appUrl, 0, strlen($appUrl) - 1);
    }

    $appUrl .=
      '/' .
      Language::LANGUAGE_KEY_AM .
      '/home-page?action=account_verification&status=' .
      $status;

    return redirect($appUrl);
  }
}
