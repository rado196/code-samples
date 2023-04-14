<?php

namespace App\Http\Controllers\Student\Training;

use App\Http\Controllers\Controller;
use App\Models\StudentAppointment;
use App\Models\StudentTheoreticalPartTraining;
use App\Models\TheoreticalPartTraining\TheoreticalPartLesson;
use App\Models\TheoreticalPartTraining\TheoreticalPartTraining;
use App\Models\TheoreticalPartTraining\TheoreticalPartTrainingPrice;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TheoreticalPartController extends Controller
{
  // private function checkIsFirstVideo($videos, $lessonId, $trainingId)
  // {
  //   $firstLesson = TheoreticalPartLesson::first();
  //   $firstTraining = TheoreticalPartTraining::query()
  //     ->where('lesson_id', $lessonId)
  //     ->first();

  //   if (count($videos) > 0) {
  //     foreach ($videos as $key => $video) {
  //       $video->is_first = false;
  //       if (
  //         $firstLesson->id == $lessonId &&
  //         $firstTraining->id == $trainingId
  //       ) {
  //         if ($key === 0) {
  //           $video->is_first = true;
  //         }
  //       }
  //     }
  //   }
  // }

  private function checkIsFirstVideo($trainings, $lessonId)
  {
    $firstLesson = TheoreticalPartLesson::first();
    $firstTraining = TheoreticalPartTraining::query()
      ->where('lesson_id', $lessonId)
      ->first();

    if (count($trainings) > 0) {
      foreach ($trainings as $training) {
        foreach ($training->videos as $key => $video) {
          $video->is_first = false;
          if (
            $firstLesson->id == $lessonId &&
            $firstTraining->id == $training->id
          ) {
            if ($key === 0) {
              $video->is_first = true;
            }
          }
        }
      }
    }
  }

  /**
   * GET: /api/student/trainings/theoretical-part/lessons
   */
  public function getTheoreticalPartLessons(Request $request)
  {
    $theoreticalPartLessons = TheoreticalPartLesson::query()
      ->without('translations')
      ->with('trainings')
      ->get();

    return response()->json([
      'theoretical_part_lessons' => $theoreticalPartLessons,
    ]);
  }

  /**
   * GET: /api/student/trainings/theoretical-part/lessons/{id}
   */
  public function getTheoreticalPartLessonTraining(Request $request, $id)
  {
    $authId = Auth::id();

    $theoreticalPartLesson = TheoreticalPartLesson::query()
      ->whereId($id)
      ->without('translations')
      ->with([
        'trainings' => function ($query) {
          $query->with('videos');
        },
        'examGroup' => function ($query) {
          $query->with([
            'questions' => function ($query) {
              $query->with('answers', 'explanation', 'translation');
            },
          ]);
        },
      ])
      ->first();

    $this->checkIsFirstVideo($theoreticalPartLesson->trainings, $id);

    $content = StudentTheoreticalPartTraining::query()
      ->where('student_id', $authId)
      ->where('status', StudentTheoreticalPartTraining::STATUS_PAID)
      ->whereNull('expiration_date')
      ->exists();

    $coursePrice = TheoreticalPartTrainingPrice::first();

    return response()->json([
      'lesson' => $theoreticalPartLesson,
      'content' => $content ? true : 'permission_denied',
      'course_price' => $coursePrice,
      'status' => 'success',
    ]);
  }

  // public function getTheoreticalPartLessonTraining(Request $request, $id)
  // {
  //   $authId = Auth::id();

  //   $theoreticalPartLessonTraining = TheoreticalPartTraining::query()
  //     ->whereId($id)
  //     ->without('translations')
  //     ->with('videos')
  //     ->first();

  //   $content = StudentTheoreticalPartTraining::query()
  //     ->where('student_id', $authId)
  //     ->where('status', StudentTheoreticalPartTraining::STATUS_PAID)
  //     ->whereNull('expiration_date')
  //     ->exists();

  //   $training = TheoreticalPartTraining::find($id);

  //   $this->checkIsFirstVideo(
  //     $theoreticalPartLessonTraining->videos,
  //     $training->lesson_id,
  //     $training->id
  //   );

  //   $coursePrice = TheoreticalPartTrainingPrice::first();

  //   return response()->json([
  //     'theoretical_part_lesson_training' => $theoreticalPartLessonTraining,
  //     'content' => $content ? true : 'permission_denied',
  //     'course_price' => $coursePrice,
  //     'status' => 'success',
  //   ]);
  // }

  /**
   * GET: /api/student/trainings/theoretical-part/my-trainings
   */
  public function getTheoreticalPartLessonMyTraining(Request $request)
  {
    $authId = Auth::id();

    $myTrainings = TheoreticalPartTraining::all();

    $content = StudentTheoreticalPartTraining::query()
      ->where('student_id', $authId)
      ->where('status', StudentTheoreticalPartTraining::STATUS_PAID)
      ->whereNull('expiration_date')
      ->exists();

    $coursePrice = TheoreticalPartTrainingPrice::first();

    $appointments = StudentAppointment::query()
      ->where('student_id', $authId)
      ->whereNotIn('status', [
        StudentAppointment::STATUS_CANCELED,
        StudentAppointment::STATUS_EXPIRED,
      ])
      ->orderBy('date', 'DESC')
      ->get();

    return response()->json([
      'my_trainings' => $myTrainings,
      'content' => $content ? true : 'permission_denied',
      'con' => $content,
      'course_price' => $coursePrice,
      'appointments' => $appointments,
      'status' => 'success',
    ]);
  }

  /**
   * POST: /api/student/trainings/theoretical-part/lessons/buy
   */
  public function buyCourse(Request $request)
  {
    $authId = Auth::id();
    $studentWallet = Wallet::query()
      ->where('user_id', $authId)
      ->first();

    $coursePrice = TheoreticalPartTrainingPrice::first();

    $studentTraining = new StudentTheoreticalPartTraining();
    $studentTraining->student_id = $authId;

    if ($studentWallet->balance >= $coursePrice->price) {
      $studentWallet->decrement('balance', $coursePrice->price);
      $studentTraining->status = StudentTheoreticalPartTraining::STATUS_PAID;
    } else {
      $studentTraining->status = StudentTheoreticalPartTraining::STATUS_PENDING;
    }

    $studentTraining->save();

    return response()->json([
      'action_status' => $studentTraining->status,
      'status' => 'success',
    ]);
  }
}
