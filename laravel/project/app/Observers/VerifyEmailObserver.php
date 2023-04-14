<?php

namespace App\Observers;

use App\Contracts\Models\BaseModelObserver;
use App\Models\User;
use App\Models\VerifyEmail;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Str;

class VerifyEmailObserver extends BaseModelObserver
{
  protected static function model()
  {
    return VerifyEmail::class;
  }

  /**
   * Handle the VerifyEmail "creating" event.
   *
   * @param  \App\Models\VerifyEmail  $verifyEmail
   * @return void
   */
  public function creating(VerifyEmail $verifyEmail)
  {
    $verifyEmail->verify_token = Str::random(64);
  }

  /**
   * Handle the VerifyEmail "created" event.
   *
   * @param  \App\Models\VerifyEmail  $verifyEmail
   * @return void
   */
  public function created(VerifyEmail $verifyEmail)
  {
    $user = User::find($verifyEmail->user_id);
    $userFullName = $user->first_name . ' ' . $user->last_name;
    $notification = new VerifyEmailNotification(
      $verifyEmail->verify_token,
      $userFullName
    );

    notify_now($user, $notification);
  }

  /**
   * Handle the VerifyEmail "updated" event.
   *
   * @param  \App\Models\VerifyEmail  $verifyEmail
   * @return void
   */
  public function updated(VerifyEmail $verifyEmail)
  {
    //
  }

  /**
   * Handle the VerifyEmail "deleted" event.
   *
   * @param  \App\Models\VerifyEmail  $verifyEmail
   * @return void
   */
  public function deleted(VerifyEmail $verifyEmail)
  {
    //
  }

  /**
   * Handle the VerifyEmail "restored" event.
   *
   * @param  \App\Models\VerifyEmail  $verifyEmail
   * @return void
   */
  public function restored(VerifyEmail $verifyEmail)
  {
    //
  }

  /**
   * Handle the VerifyEmail "force deleted" event.
   *
   * @param  \App\Models\VerifyEmail  $verifyEmail
   * @return void
   */
  public function forceDeleted(VerifyEmail $verifyEmail)
  {
    //
  }
}
