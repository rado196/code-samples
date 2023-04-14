<?php

namespace Tests\Feature;

use App\Models\ExamGroup\ExamGroupQuestion;
use App\Models\ExamTests\ExamTest as StudentExamTest;
use App\Models\ExamTests\ExamTestQuestion;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExamTest extends TestCase
{
  const EXAM_TEST_QUESTIONS_COUNT = 20;

  private function getInitialParams()
  {
    $examTests = StudentExamTest::query()
      ->where('is_valid', true)
      ->without(['translation', 'translations', 'questions'])
      ->pluck('id');

    $examTestQuestions = ExamTestQuestion::query()
      ->whereIn('exam_test_id', $examTests)
      ->without(['translation', 'translations', 'answers'])
      ->get();

    $groupedExamTestQuestions = $examTestQuestions->groupBy('exam_test_id');

    $examGroupQuestionIds = ExamGroupQuestion::query()
      ->pluck('id')
      ->toArray();

    $examTestGroupQuestionIds = $examTestQuestions
      ->pluck('exam_group_question_id')
      ->toArray();

    return [
      'examTests' => $examTests,
      'examTestQuestions' => $examTestQuestions,
      'groupedExamTestQuestions' => $groupedExamTestQuestions,
      'examGroupQuestionIds' => $examGroupQuestionIds,
      'examTestGroupQuestionIds' => $examTestGroupQuestionIds,
    ];
  }

  public function test_it_should_use_20_questions_in_one_test()
  {
    $initialParams = $this->getInitialParams();
    $examTests = $initialParams['examTests'];
    $groupedExamTestQuestions = $initialParams['groupedExamTestQuestions'];

    foreach ($examTests as $id) {
      $this->assertEquals(
        count($groupedExamTestQuestions[$id]),
        self::EXAM_TEST_QUESTIONS_COUNT
      );
    }
  }

  public function test_it_should_contains_unique_questions()
  {
    $initialParams = $this->getInitialParams();
    $examTests = $initialParams['examTests'];
    $groupedExamTestQuestions = $initialParams['groupedExamTestQuestions'];

    foreach ($examTests as $id) {
      $questionIds = [];
      foreach ($groupedExamTestQuestions[$id] as $question) {
        $questionIds[] = $question['exam_group_question_id'];
      }

      $result = array_unique($questionIds);

      $this->assertEquals(count($result), self::EXAM_TEST_QUESTIONS_COUNT);
    }
  }

  public function test_is_should_use_all_questions_in_database()
  {
    $initialParams = $this->getInitialParams();
    $examGroupQuestionIds = $initialParams['examGroupQuestionIds'];
    $examTestGroupQuestionIds = $initialParams['examTestGroupQuestionIds'];

    $uArr1 = array_unique($examGroupQuestionIds);
    $uArr2 = array_unique($examTestGroupQuestionIds);

    $this->assertEquals(count($uArr1), count($uArr2));
  }
}
