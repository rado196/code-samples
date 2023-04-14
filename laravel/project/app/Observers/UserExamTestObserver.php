<?php

namespace App\Observers;

use App\Contracts\Models\BaseModelObserver;
use App\Models\UserExamTests\UserExamTest;

class UserExamTestObserver extends BaseModelObserver
{
  protected static function model()
  {
    return UserExamTest::class;
  }

  /**
   * Handle the UserExamTest "creating" event.
   *
   * @param  \App\Models\UserExamTest  $userExamTest
   * @return void
   */
  public function creating(UserExamTest $userExamTest)
  {
    //
  }

  /**
   * Handle the UserExamTest "created" event.
   *
   * @param  \App\Models\UserExamTest  $userExamTest
   * @return void
   */
  public function created(UserExamTest $userExamTest)
  {
    //
  }

  /**
   * Handle the UserExamTest "updating" event.
   *
   * @param  \App\Models\UserExamTest  $userExamTest
   * @return void
   */
  public function updating(UserExamTest $userExamTest)
  {
    // dd($userExamTest->isDirty('is_completed'));
    // if ($userExamTest->isDirty('is_completed')) {
    //   $createdAt = $userExamTest->created_at->getTimestamp();
    //   $updatedAt = $userExamTest->updated_at->getTimestamp();

    //   $finishTime = $updatedAt - $createdAt;
    //   dd($finishTime);

    //   $userExamTest->finish_time = $finishTime;
    // }
  }

  /**
   * Handle the UserExamTest "updated" event.
   *
   * @param  \App\Models\UserExamTest  $userExamTest
   * @return void
   */
  public function updated(UserExamTest $userExamTest)
  {
    //
  }

  /**
   * Handle the UserExamTest "deleted" event.
   *
   * @param  \App\Models\UserExamTest  $userExamTest
   * @return void
   */
  public function deleted(UserExamTest $userExamTest)
  {
    //
  }

  /**
   * Handle the UserExamTest "restored" event.
   *
   * @param  \App\Models\UserExamTest  $userExamTest
   * @return void
   */
  public function restored(UserExamTest $userExamTest)
  {
    //
  }

  /**
   * Handle the UserExamTest "force deleted" event.
   *
   * @param  \App\Models\UserExamTest  $userExamTest
   * @return void
   */
  public function forceDeleted(UserExamTest $userExamTest)
  {
    //
  }
}
