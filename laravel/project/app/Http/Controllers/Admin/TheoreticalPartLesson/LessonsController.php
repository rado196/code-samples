<?php

namespace App\Http\Controllers\Admin\TheoreticalPartLesson;

use App\Http\Controllers\Controller;
use App\Models\TheoreticalPartTraining\TheoreticalPartLesson;
use App\Models\TheoreticalPartTraining\TheoreticalPartTrainingPrice;
use Illuminate\Http\Request;

class LessonsController extends Controller
{
  /**
   * GET: /api/admin/theoretical-part-lessons
   */
  public function getLessons(Request $request)
  {
    $lessons = TheoreticalPartLesson::query()
      ->with(['translations', 'translation', 'examGroup'])
      ->get();

    $price = TheoreticalPartTrainingPrice::first();

    return response()->json([
      'lessons' => $lessons,
      'price' => $price,
      'status' => 'success',
    ]);
  }

  /**
   * PUT: /api/admin/theoretical-part-lessons/{id}
   */
  public function updateLesson(Request $request, $lessonId)
  {
    $examGroupId = $request->post('examGroupId');

    $lesson = TheoreticalPartLesson::find($lessonId);

    if (isset($examGroupId)) {
      if ('null' === $examGroupId || is_null($examGroupId)) {
        $examGroupId = null;
      }

      $lesson->exam_group_id = $examGroupId;
    }

    $lesson->save();

    return response()->json([
      'status' => 'success',
      'lesson' => $lesson,
    ]);
  }

  /**
   * POST: /api/admin/theoretical-part-lessons/price
   */
  public function setPrice(Request $request)
  {
    $price = TheoreticalPartTrainingPrice::first();

    if ($price) {
      $price->price = $request->price;
    } else {
      $price = new TheoreticalPartTrainingPrice();
      $price->price = $request->price;
    }

    $price->save();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
