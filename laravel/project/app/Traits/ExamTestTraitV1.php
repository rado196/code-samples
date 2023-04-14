<?php

namespace App\Traits;

use App\Models\ExamGroup\ExamGroup;
use App\Models\ExamGroup\ExamGroupAnswer;
use App\Models\ExamGroup\ExamGroupQuestion;
use App\Models\ExamTests\ExamTest;
use App\Models\ExamTests\ExamTestAnswer;
use App\Models\ExamTests\ExamTestQuestion;
use App\Models\ExamTests\ExamTestTranslation;
use App\Models\Language;

trait ExamTestTraitV1
{
  private function generateExamTest($count, $duration, $maxWrongAnswers, $data)
  {
    $am = Language::query()
      ->where('country_code', Language::LANGUAGE_KEY_AM)
      ->first();

    $ru = Language::query()
      ->where('country_code', Language::LANGUAGE_KEY_RU)
      ->first();

    $en = Language::query()
      ->where('country_code', Language::LANGUAGE_KEY_EN)
      ->first();

    for ($i = 1; $i <= $count; ++$i) {
      $examTest = ExamTest::create([
        'duration' => $duration,
        'max_wrong_answers' => $maxWrongAnswers,
      ]);

      $translations = [
        [
          'title' => 'Թեստ ' . $i,
          'language_id' => $am->id,
        ],
        [
          'title' => 'Test ' . $i,
          'language_id' => $en->id,
        ],
        [
          'title' => 'Тест ' . $i,
          'language_id' => $ru->id,
        ],
      ];

      foreach ($translations as $translation) {
        ExamTestTranslation::create([
          'exam_test_id' => $examTest->id,
          'title' => $translation['title'],
          'language_id' => $translation['language_id'],
        ]);
      }

      $this->_createExamTestQuestions($examTest->id, $data);
    }
  }

  private function _createExamTestQuestions($examTestId, $data)
  {
    $alreadyUsedExamGroups = [];

    foreach ($data as $item) {
      $examGroup = ExamGroup::query()
        ->inRandomOrder()
        ->whereNotIn('id', $alreadyUsedExamGroups)
        ->where('question_category_id', $item->categoryId)
        ->first();

      $alreadyUsedExamGroups[] = $examGroup->id;

      $randomExamGroupQuestions = ExamGroupQuestion::query()
        ->inRandomOrder()
        ->with('explanation')
        ->where('exam_group_id', $examGroup->id)
        ->limit($item->questionCount)
        ->get();

      foreach ($randomExamGroupQuestions as $randomExamGroupQuestion) {
        $examTestQuestion = ExamTestQuestion::create([
          'exam_test_id' => $examTestId,
          'exam_group_question_id' => $randomExamGroupQuestion->id,
          'explanation_id' => $randomExamGroupQuestion->explanation->id,
        ]);

        $this->_createExamTestAnswers(
          $examTestId,
          $examTestQuestion->id,
          $randomExamGroupQuestion->id
        );
      }
    }
  }

  private function _createExamTestAnswers(
    $examTestId,
    $examTestQuestionId,
    $randomExamGroupQuestionId
  ) {
    $answers = ExamGroupAnswer::query()
      ->where('question_id', $randomExamGroupQuestionId)
      ->get();

    foreach ($answers as $answer) {
      ExamTestAnswer::create([
        'exam_test_id' => $examTestId,
        'exam_test_question_id' => $examTestQuestionId,
        'exam_group_answer_id' => $answer->id,
      ]);
    }
  }
}
