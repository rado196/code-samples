<?php

namespace App\Observers;

use App\Contracts\Models\BaseModelObserver;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Str;

class PasswordResetObserver extends BaseModelObserver
{
  protected static function model()
  {
    return PasswordReset::class;
  }

  /**
   * Handle the PasswordReset "creating" event.
   *
   * @param  \App\Models\PasswordReset  $passwordReset
   * @return void
   */
  public function creating(PasswordReset $passwordReset)
  {
    $passwordReset->reset_token = Str::random(64);
  }

  /**
   * Handle the PasswordReset "created" event.
   *
   * @param  \App\Models\PasswordReset  $passwordReset
   * @return void
   */
  public function created(PasswordReset $passwordReset)
  {
    $user = User::find($passwordReset->user_id);
    $notification = new PasswordResetNotification($passwordReset->reset_token);

    notify_now($user, $notification);
  }

  /**
   * Handle the PasswordReset "updated" event.
   *
   * @param  \App\Models\PasswordReset  $passwordReset
   * @return void
   */
  public function updated(PasswordReset $passwordReset)
  {
    //
  }

  /**
   * Handle the PasswordReset "deleted" event.
   *
   * @param  \App\Models\PasswordReset  $passwordReset
   * @return void
   */
  public function deleted(PasswordReset $passwordReset)
  {
    //
  }

  /**
   * Handle the PasswordReset "restored" event.
   *
   * @param  \App\Models\PasswordReset  $passwordReset
   * @return void
   */
  public function restored(PasswordReset $passwordReset)
  {
    //
  }

  /**
   * Handle the PasswordReset "force deleted" event.
   *
   * @param  \App\Models\PasswordReset  $passwordReset
   * @return void
   */
  public function forceDeleted(PasswordReset $passwordReset)
  {
    //
  }
}
