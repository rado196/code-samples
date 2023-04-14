<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorRating;
use App\Models\StudentAppointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorsController extends Controller
{
  private function canCreateRating($instructors)
  {
    foreach ($instructors as $instructor) {
      $canCreateRating = false;

      $isExistsAppointment = StudentAppointment::query()
        ->where('instructor_id', $instructor->id)
        ->where('student_id', Auth::id())
        ->where('status', StudentAppointment::STATUS_COMPLETED)
        ->exists();

      if ($isExistsAppointment) {
        $isExistsRating = InstructorRating::query()
          ->where('instructor_id', $instructor->id)
          ->where('student_id', Auth::id())
          ->exists();

        if (!$isExistsRating) {
          $canCreateRating = true;
        }
      }

      $instructor->can_create_rating = $canCreateRating;
    }

    return $instructors;
  }

  /**
   * GET: /api/instructors
   */

  public function getInstructors(Request $request)
  {
    $instructors = User::query()
      ->where('role', User::ROLE_INSTRUCTOR)
      ->with('instructor_day_offs')
      ->with('instructor_work_detail', function ($query) {
        $query->where('hidden', false);
      })
      ->get();

    return response()->json([
      'instructors' => $instructors,
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/instructors/with-hidden/{studentId}
   */

  public function getInstructorWithHidden(Request $request)
  {
    $instructors = User::query()
      ->where('role', User::ROLE_INSTRUCTOR)
      ->with([
        'instructor_day_offs',
        'instructor_work_detail',
        'instructor_info',
      ])
      ->whereDoesntHave('hidden_instructor_students', function ($query) {
        $query->where('student_id', '!=', Auth::id());
      })
      ->get();

    return response()->json([
      'instructors' => $this->canCreateRating($instructors),
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/instructors/{id}
   */
  public function getInstructor(Request $request, $id)
  {
    $instructors = User::query()
      ->whereId($id)
      ->where('role', User::ROLE_INSTRUCTOR)
      ->with(['instructor_work_detail', 'instructor_day_offs'])
      ->first();

    return response()->json([
      'instructors' => $instructors,
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/instructors/{id}/ratings
   */
  public function getInstructorRatings(Request $request, $id)
  {
    $instructorRatings = InstructorRating::query()
      ->where('instructor_id', $id)
      ->where('rating', '>=', 3)
      ->with('student', function ($query) {
        return $query->select('id', 'first_name', 'last_name');
      })
      ->get();

    return response()->json([
      'ratings' => $instructorRatings,
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/instructors/count
   */
  public function getInstructorCount(Request $request)
  {
    $instructors = User::query()
      ->where('role', User::ROLE_INSTRUCTOR)
      ->count();

    return response()->json([
      'count' => $instructors,
      'status' => 'success',
    ]);
  }
}
