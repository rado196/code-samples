<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePassword\ChangePasswordRequest;
use App\Models\User;
use App\Traits\AuthenticationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
  use AuthenticationTrait;

  /**
   * PUT: /api/users/password
   */
  public function change(ChangePasswordRequest $request)
  {
    return $this->withStandardUser(Auth::user(), function (User $user) use (
      $request
    ) {
      $oldPassword = $request->post('old_password');
      $newPassword = $request->post('new_password');

      if (!Hash::check($oldPassword, $user->password)) {
        return response()->json([
          'status' => 'failure',
          'errors' => [
            'old_password' => ['auth_error_incorrect_old_password'],
          ],
        ]);
      }

      $user->password = Hash::make($newPassword);
      $user->save();

      return response()->json([
        'status' => 'success',
      ]);
    });
  }
}
