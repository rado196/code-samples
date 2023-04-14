<?php

namespace App\Http\Controllers\Student\ExamTests;

use App\Http\Controllers\Controller;
use App\Models\ExamTests\ExamTest;
use App\Models\ExamTests\ExamTestAnswer;
use App\Models\UserExamTests\UserExamTest;
use App\Models\UserExamTests\UserExamTestQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamTestsController extends Controller
{
  private function setUserExamTestFinishTime($userExamTest)
  {
    $createdAt = $userExamTest->created_at->getTimestamp();
    $updatedAt = $userExamTest->updated_at->getTimestamp();

    $finishTime = $updatedAt - $createdAt;

    $userExamTest->finish_time = $finishTime;
    $userExamTest->save();
  }

  /**
   * GET: /api/student/exam-tests
   */
  public function getExamTests(Request $request)
  {
    $relations = ['translations', 'translation'];

    if (Auth::check()) {
      $relations = [
        ...$relations,
        'user_exam_tests' => function ($query) {
          return $query
            ->withCount([
              'user_exam_test_questions' => function ($query) {
                return $query->where('is_right', true);
              },
            ])
            ->where('is_completed', true)
            ->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->limit(3);
        },
      ];
    }

    $examTests = ExamTest::query()
      ->with($relations)
      ->without('questions')
      ->where('is_valid', true)
      ->get();

    return response()->json([
      'exam_tests' => $examTests,
    ]);
  }

  /**
   * GET: /api/student/exam-tests/{id}
   */
  public function getExamTest(Request $request, $id)
  {
    $userExamTest = UserExamTest::query()
      ->with(['exam_test', 'user_exam_test_questions'])
      ->where('unique_id', $id)
      ->first();

    return response()->json([
      'user_exam_test' => $userExamTest,
    ]);
  }

  /**
   * POST: /api/student/exam-tests/{id}
   */
  public function generateNewExamTest(Request $request, $id)
  {
    $uniqueId = unique_code(9);

    UserExamTest::create([
      'user_id' => Auth::id(),
      'exam_test_id' => $id,
      'unique_id' => $uniqueId,
    ]);

    return response()->json([
      'unique_id' => $uniqueId,
    ]);
  }

  /**
   * POST: /api/student/exam-tests/{examTestUniqueId}/answer
   */
  public function chooseAnswer(Request $request, $examTestUniqueId)
  {
    $answerId = $request->post('answer_id');
    $questionsCount = $request->post('questions_count');
    $isRight = $request->post('is_right');
    $selectRightAnswersCount = null;
    $isTestPassed = null;

    $userExamTest = UserExamTest::query()
      ->where('unique_id', $examTestUniqueId)
      ->first();
    $examTestAnswer = ExamTestAnswer::query()
      ->where('exam_group_answer_id', $answerId)
      ->first();

    UserExamTestQuestion::create([
      'user_exam_test_id' => $userExamTest->id,
      'exam_test_question_id' => $examTestAnswer->exam_test_question_id,
      'exam_test_answer_id' => $examTestAnswer->id,
      'is_right' => $isRight,
    ]);

    $userExamTestQuestionsQuery = UserExamTestQuestion::query()->where(
      'user_exam_test_id',
      $userExamTest->id
    );

    $examTest = ExamTest::query()
      ->withCount('questions')
      ->whereId($userExamTest->exam_test_id)
      ->first();

    $allCount = clone $userExamTestQuestionsQuery;
    $onlyRightAnswersCount = clone $userExamTestQuestionsQuery;

    if ($allCount->count() == $questionsCount) {
      $userExamTest->is_completed = true;
      $userExamTest->save();

      $this->setUserExamTestFinishTime($userExamTest);
    }

    if ($userExamTest->is_completed) {
      $selectRightAnswersCount = $onlyRightAnswersCount
        ->where('is_right', true)
        ->count();
      $minimalMarkToPass =
        $examTest->questions_count - $examTest->max_wrong_answers;

      $isTestPassed = $minimalMarkToPass <= $selectRightAnswersCount;
    }

    return response()->json([
      'right_answers_count' => $selectRightAnswersCount,
      'is_success' => $isTestPassed,
      'finish_time' => $userExamTest->finish_time,
    ]);
  }

  public function expiredExamTest(Request $request, $examTestUniqueId)
  {
    $selectRightAnswersCount = null;
    $isTestPassed = null;

    $userExamTest = UserExamTest::query()
      ->where('unique_id', $examTestUniqueId)
      ->first();

    $userExamTest->is_completed = true;
    $userExamTest->save();

    $examTest = ExamTest::query()
      ->withCount('questions')
      ->whereId($userExamTest->exam_test_id)
      ->first();

    $userExamTestQuestionsQuery = UserExamTestQuestion::query()->where(
      'user_exam_test_id',
      $userExamTest->id
    );

    $onlyRightAnswersCount = clone $userExamTestQuestionsQuery;

    if ($userExamTest->is_completed) {
      $selectRightAnswersCount = $onlyRightAnswersCount
        ->where('is_right', true)
        ->count();
      $minimalMarkToPass =
        $examTest->questions_count - $examTest->max_wrong_answers;

      $isTestPassed = $minimalMarkToPass <= $selectRightAnswersCount;
      $this->setUserExamTestFinishTime($userExamTest);
    }

    return response()->json([
      'right_answers_count' => $selectRightAnswersCount,
      'is_success' => $isTestPassed,
      'finish_time' => $userExamTest->finish_time,
    ]);
  }
}
