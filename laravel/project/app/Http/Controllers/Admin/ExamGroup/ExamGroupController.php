<?php

namespace App\Http\Controllers\Admin\ExamGroup;

use App\Http\Controllers\Controller;
use App\Models\ExamGroup\ExamGroup;
use App\Models\ExamGroup\ExamGroupTranslation;
use Illuminate\Http\Request;

class ExamGroupController extends Controller
{
  private function createTranslations(Request $request, $examGroupId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      ExamGroupTranslation::create([
        'exam_group_id' => $examGroupId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($examGroupId)
  {
    ExamGroupTranslation::query()
      ->where('exam_group_id', $examGroupId)
      ->delete();
  }

  /**
   * GET: /api/admin/exam-groups
   */
  public function getExamGroups()
  {
    $data = ExamGroup::query()
      ->with(['translations', 'translation', 'category'])
      ->withCount('questions')
      ->get();

    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * GET: /api/admin/exam-groups/{id}
   */
  public function getExamGroup($id)
  {
    $examGroup = ExamGroup::query()
      ->with(['translations', 'translation'])
      ->whereId($id)
      ->first();

    return response()->json([
      'exam_group' => $examGroup,
    ]);
  }

  /**
   * POST: /api/admin/exam-groups
   */
  public function addExamGroup(Request $request)
  {
    $questionCategoryId = $request->post('question_category_id');

    $examGroup = ExamGroup::create([
      'question_category_id' => $questionCategoryId,
    ]);

    $this->createTranslations($request, $examGroup->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/exam-groups/{id}
   */
  public function updateExamGroup(Request $request, $id)
  {
    $questionCategoryId = $request->post('question_category_id');

    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    $examGroup = ExamGroup::find($id);
    $examGroup->question_category_id = $questionCategoryId;
    $examGroup->save();

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/exam-groups/{id}
   */
  public function deleteExamGroup($id)
  {
    $this->cleanupTranslations($id);

    ExamGroup::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
