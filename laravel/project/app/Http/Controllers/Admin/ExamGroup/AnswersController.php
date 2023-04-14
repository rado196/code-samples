<?php

namespace App\Http\Controllers\Admin\ExamGroup;

use App\Http\Controllers\Controller;
use App\Models\ExamGroup\ExamGroupAnswer;
use App\Models\ExamGroup\ExamGroupAnswerTranslation;
use App\Models\ExamGroup\ExamGroupQuestion;
use Illuminate\Http\Request;

class AnswersController extends Controller
{
  private function createTranslations(Request $request, $examGroupAnswerId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      ExamGroupAnswerTranslation::create([
        'exam_group_answer_id' => $examGroupAnswerId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($examGroupAnswerId)
  {
    ExamGroupAnswerTranslation::query()
      ->where('exam_group_answer_id', $examGroupAnswerId)
      ->delete();
  }

  /**
   * GET: /api/admin/exam-groups/{examGroupId}/questions/{examGroupQuestionId}/answers
   */
  public function getAnswers(
    Request $request,
    $examGroupId,
    $examGroupQuestionId
  ) {
    $question = ExamGroupQuestion::query()
      ->with('translation')
      ->whereId($examGroupQuestionId)
      ->first();

    $answers = ExamGroupAnswer::query()
      ->with(['translations', 'translation'])
      ->where('question_id', $examGroupQuestionId)
      ->get();

    return response()->json([
      'answers' => $answers,
      'question' => $question,
    ]);
  }

  /**
   * GET: /api/admin/exam-groups/{examGroupId}/questions/{examGroupQuestionId}/answers/{id}
   */
  public function getAnswer(
    Request $request,
    $examGroupId,
    $examGroupQuestionId,
    $id
  ) {
    $examGroupAnswer = ExamGroupAnswer::query()
      ->with(['translations', 'translation'])
      ->whereId($id)
      ->first();

    return response()->json([
      'exam_group_answer' => $examGroupAnswer,
    ]);
  }

  /**
   * POST: /api/admin/exam-groups/{examGroupId}/questions/{examGroupQuestionId}/answers
   */
  public function addAnswer(
    Request $request,
    $examGroupId,
    $examGroupQuestionId
  ) {
    $ifExistsRow = ExamGroupAnswer::query()
      ->where('question_id', $examGroupQuestionId)
      ->exists();

    $examGroupAnswer = ExamGroupAnswer::create([
      'is_right' => !$ifExistsRow,
      'exam_group_id' => $examGroupId,
      'question_id' => $examGroupQuestionId,
    ]);

    $this->createTranslations($request, $examGroupAnswer->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/exam-groups/{examGroupId}/questions/{examGroupQuestionId}/answers/{id}
   */
  public function updateAnswer(
    Request $request,
    $examGroupId,
    $examGroupQuestionId,
    $id
  ) {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * PUT: /api/admin/exam-groups/{examGroupId}/questions/{examGroupQuestionId}/answers/{id}/right-answer
   */
  public function setRightAnswer(
    Request $request,
    $examGroupId,
    $examGroupQuestionId,
    $id
  ) {
    ExamGroupAnswer::query()
      ->where('question_id', $examGroupQuestionId)
      ->update([
        'is_right' => false,
      ]);

    $answer = ExamGroupAnswer::find($id);
    $answer->is_right = true;
    $answer->save();

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/exam-groups/{examGroupId}/questions/{examGroupQuestionId}/answers/{id}
   */
  public function deleteAnswer(
    Request $request,
    $examGroupId,
    $examGroupQuestionId,
    $id
  ) {
    $this->cleanupTranslations($id);

    ExamGroupAnswer::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
