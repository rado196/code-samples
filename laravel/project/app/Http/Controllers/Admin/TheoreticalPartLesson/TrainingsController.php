<?php

namespace App\Http\Controllers\Admin\TheoreticalPartLesson;

use App\Http\Controllers\Controller;
use App\Models\TheoreticalPartTraining\TheoreticalPartLesson;
use App\Models\TheoreticalPartTraining\TheoreticalPartTraining;
use App\Models\TheoreticalPartTraining\TheoreticalPartTrainingTranslation;
use Illuminate\Http\Request;

class TrainingsController extends Controller
{
  private function createTranslations(Request $request, $lessonId, $trainingId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      TheoreticalPartTrainingTranslation::create([
        'training_id' => $trainingId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
        'description' => $singleData->description,
      ]);
    }
  }

  private function cleanupTranslations($trainingId)
  {
    TheoreticalPartTrainingTranslation::query()
      ->where('training_id', $trainingId)
      ->delete();
  }

  /**
   * GET: /api/admin/theoretical-part-lessons/{lessonId}/trainings
   */
  public function getTrainings(Request $request, $lessonId)
  {
    $lesson = TheoreticalPartLesson::query()
      ->whereId($lessonId)
      ->withOut(['translations'])
      ->first();

    $trainings = TheoreticalPartTraining::query()
      ->where('lesson_id', $lessonId)
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'lesson' => $lesson,
      'trainings' => $trainings,
    ]);
  }

  /**
   * GET: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{id}
   */
  public function getTraining(Request $request, $lessonId, $id)
  {
    $training = TheoreticalPartTraining::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'training' => $training,
    ]);
  }

  /**
   * POST: /api/admin/theoretical-part-lessons/{lessonId}/trainings
   */
  public function addTraining(Request $request, $lessonId)
  {
    $training = TheoreticalPartTraining::create([
      'lesson_id' => $lessonId,
    ]);

    // $this->uploadImages($request, $trafficRule->id);
    $this->createTranslations($request, $lessonId, $training->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{id}
   */
  public function updateTraining(Request $request, $lessonId, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $lessonId, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{id}
   */
  public function deleteTraining(Request $request, $lessonId, $id)
  {
    $this->cleanupTranslations($id);

    TheoreticalPartTraining::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
