<?php

namespace App\Http\Controllers\Admin\ExamGroup;

use App\Http\Controllers\Controller;
use App\Models\ExamGroup\ExamGroupExplanation;
use App\Models\ExamGroup\ExamGroupExplanationTranslation;
use App\Models\ExamGroup\ExamGroupQuestion;
use App\Models\ExamGroup\ExamGroupQuestionTranslation;
use App\Traits\UploadingTrait;
use Illuminate\Http\Request;

class ExamGroupQuestionsController extends Controller
{
  use UploadingTrait;

  private function uploadImage(Request $request)
  {
    if ($request->hasFile('image')) {
      return $this->uploadFile(
        $request->file('image'),
        self::$UPLOAD_FOLDER_EXAM_TEST_QUESTIONS
      );
    }

    return null;
  }

  private function cleanupImage($examGroupQuestionId)
  {
    $image = ExamGroupQuestion::query()
      ->whereId($examGroupQuestionId)
      ->first();

    if (!is_null($image->image)) {
      $file = storage_path(
        'app/public/uploads/exam-test-questions/' . $image->image
      );
      if (file_exists($file)) {
        unlink($file);
      }
    }
  }

  private function createTranslations(Request $request, $examGroupQuestionId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      ExamGroupQuestionTranslation::create([
        'exam_group_question_id' => $examGroupQuestionId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($examGroupQuestionId)
  {
    ExamGroupQuestionTranslation::query()
      ->where('exam_group_question_id', $examGroupQuestionId)
      ->delete();
  }

  private function createExplanationTranslations(
    Request $request,
    $explanationId
  ) {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      ExamGroupExplanationTranslation::create([
        'explanation_id' => $explanationId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->explanation_title,
        'description' => $singleData->explanation_description,
      ]);
    }
  }

  private function cleanupExplanationTranslations($explanationId)
  {
    ExamGroupExplanationTranslation::query()
      ->where('explanation_id', $explanationId)
      ->delete();
  }

  private function createExplanation(
    Request $request,
    $examGroupId,
    $questionId
  ) {
    $explanation = ExamGroupExplanation::create([
      'exam_group_id' => $examGroupId,
      'question_id' => $questionId,
    ]);

    $this->createExplanationTranslations($request, $explanation->id);
  }

  private function updateExplanation(Request $request, $questionId)
  {
    $explanation = ExamGroupExplanation::query()
      ->where('question_id', $questionId)
      ->first();

    $this->cleanupExplanationTranslations($explanation->id);
    $this->createExplanationTranslations($request, $explanation->id);
  }

  /**
   * GET: /api/admin/exam-groups/{examGroupId}/questions
   */
  public function getQuestions($id)
  {
    $data = ExamGroupQuestion::query()
      ->where('exam_group_id', $id)
      ->with(['translations', 'translation', 'explanation', 'answers'])
      ->get();

    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * GET: /api/admin/exam-groups/{examGroupId}/questions/{id}
   */
  public function getQuestion($examGroupId, $id)
  {
    $examGroupQuestion = ExamGroupQuestion::query()
      ->with(['translations', 'translation', 'explanation'])
      ->whereId($id)
      ->first();

    return response()->json([
      'exam_group_question' => $examGroupQuestion,
    ]);
  }

  /**
   * POST: /api/admin/exam-groups/{examGroupId}/questions
   */
  public function addQuestion(Request $request, $id)
  {
    $imageName = $this->uploadImage($request);

    $examGroupQuestion = ExamGroupQuestion::create([
      'image' => $imageName,
      'exam_group_id' => $id,
    ]);

    $this->createTranslations($request, $examGroupQuestion->id);

    $this->createExplanation($request, $id, $examGroupQuestion->id);

    return response()->json(
      [
        'status' => 'success',
        'question_id' => $examGroupQuestion->id,
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/exam-groups/{examGroupId}/questions/{id}
   */
  public function updateQuestion(Request $request, $examGroupId, $questionId)
  {
    $this->cleanupImage($questionId);
    $imageName = $this->uploadImage($request);

    $this->cleanupTranslations($questionId);
    $this->createTranslations($request, $questionId);

    $this->updateExplanation($request, $questionId);

    $question = ExamGroupQuestion::find($questionId);
    $question->image = $imageName;
    $question->save();

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/exam-groups/{examGroupId}/questions/{id}
   */
  public function deleteQuestion($examGroupId, $questionId)
  {
    $this->cleanupImage($questionId);
    $this->cleanupTranslations($questionId);

    ExamGroupQuestion::query()
      ->whereId($questionId)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
