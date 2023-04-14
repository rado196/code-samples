<?php

namespace App\Traits;

use App\Models\ExamGroup\ExamGroup;
use App\Models\ExamGroup\ExamGroupAnswer;
use App\Models\ExamGroup\ExamGroupExplanation;
use App\Models\ExamTests\ExamTest;
use App\Models\ExamTests\ExamTestAnswer;
use App\Models\ExamTests\ExamTestQuestion;
use App\Models\ExamTests\ExamTestTranslation;
use App\Models\Language;

trait ExamTestTraitV2
{
  private function generateExamTest($count, $duration, $maxWrongAnswers, $data)
  {
    $questionsIdByCategoryGroup = [];
    $examGroups = ExamGroup::query()
      ->with('questions')
      ->get();

    foreach ($examGroups as $examGroup) {
      if (!isset($questionsIdByCategoryGroup[$examGroup->id])) {
        $questionsIdByCategoryGroup[$examGroup->id] = [];
      }
      foreach ($examGroup->questions as $question) {
        $questionsIdByCategoryGroup[$examGroup->id][] = $question->id;
      }
    }

    $questionCountByCategoryGroup = [];
    foreach ($data as $item) {
      $questionCountByCategoryGroup[$item->categoryId] =
        (int) $item->questionCount;
    }

    $result = $this->_generateTests(
      (int) $count,
      $questionsIdByCategoryGroup,
      $questionCountByCategoryGroup
    );

    $languages = Language::all();
    foreach ($result as $testIndex => $testQuestionsListByCategoryId) {
      $examTest = ExamTest::create([
        'duration' => $duration,
        'max_wrong_answers' => $maxWrongAnswers,
      ]);

      foreach ($languages as $language) {
        ExamTestTranslation::create([
          'exam_test_id' => $examTest->id,
          'title' => Language::buildTestIndex(
            $language->country_code,
            $testIndex + 1
          ),
          'language_id' => $language->id,
        ]);
      }

      foreach (
        $testQuestionsListByCategoryId
        as $categoryId => $questionIdList
      ) {
        foreach ($questionIdList as $questionId) {
          $explanation = ExamGroupExplanation::query()
            ->where('question_id', $questionId)
            ->first();

          $examTestQuestion = ExamTestQuestion::create([
            'exam_test_id' => $examTest->id,
            'exam_group_question_id' => $questionId,
            'explanation_id' => $explanation->id,
          ]);

          $answers = ExamGroupAnswer::query()
            ->where('question_id', $questionId)
            ->get();

          foreach ($answers as $answer) {
            ExamTestAnswer::create([
              'exam_test_id' => $examTest->id,
              'exam_test_question_id' => $examTestQuestion->id,
              'exam_group_answer_id' => $answer->id,
            ]);
          }
        }
      }
    }
  }

  private function _generateTests(
    $examTestsCount,
    $questionsIdByCategoryGroup,
    $questionCountByCategoryGroup
  ) {
    foreach ($questionsIdByCategoryGroup as $categoryId => $list) {
      shuffle($questionsIdByCategoryGroup[$categoryId]);
    }

    $questionsIdByCategoryGroupClone = json_decode(
      json_encode($questionsIdByCategoryGroup),
      true
    );

    $result = [];

    // fill initial values
    for ($i = 0; $i < $examTestsCount; ++$i) {
      if (!isset($result[$i])) {
        $result[$i] = [];
      }

      foreach ($questionCountByCategoryGroup as $categoryId => $categoryCount) {
        if (!isset($result[$i][$categoryId])) {
          $result[$i][$categoryId] = [];
        }

        for (
          $j = 0;
          count($questionsIdByCategoryGroup[$categoryId]) > 0 &&
          $j < $categoryCount;
          ++$j
        ) {
          $result[$i][$categoryId][] = array_shift(
            $questionsIdByCategoryGroup[$categoryId]
          );
        }
      }
    }

    // fill same category
    for ($i = 0; $i < $examTestsCount; ++$i) {
      foreach ($questionCountByCategoryGroup as $categoryId => $categoryCount) {
        if (
          count($result[$i][$categoryId]) ===
          $questionCountByCategoryGroup[$categoryId]
        ) {
          continue;
        }

        for (
          $j = 0;
          count($questionsIdByCategoryGroup[$categoryId]) > 0 &&
          $j < $categoryCount;
          ++$j
        ) {
          $result[$i][$categoryId][] = array_shift(
            $questionsIdByCategoryGroup[$categoryId]
          );
        }
      }
    }

    // fill missing
    for ($i = 0; $i < $examTestsCount; ++$i) {
      foreach ($questionCountByCategoryGroup as $categoryId => $categoryCount) {
        if (
          count($result[$i][$categoryId]) ===
          $questionCountByCategoryGroup[$categoryId]
        ) {
          continue;
        }

        foreach ($questionsIdByCategoryGroup as $otherCategoryId => $list) {
          if (!empty($list)) {
            $result[$i][$categoryId][] = array_shift(
              $questionsIdByCategoryGroup[$otherCategoryId]
            );

            if (
              count($result[$i][$categoryId]) ===
              $questionCountByCategoryGroup[$categoryId]
            ) {
              break;
            }
          }
        }
      }
    }

    // fill same category
    for ($i = 0; $i < $examTestsCount; ++$i) {
      foreach ($questionCountByCategoryGroup as $categoryId => $categoryCount) {
        if (
          count($result[$i][$categoryId]) ===
          $questionCountByCategoryGroup[$categoryId]
        ) {
          continue;
        }

        for ($j = count($result[$i][$categoryId]); $j < $categoryCount; ++$j) {
          for (
            $k = 0;
            $k < count($questionsIdByCategoryGroupClone[$categoryId]);
            ++$k
          ) {
            $newItem = $questionsIdByCategoryGroupClone[$categoryId][$k];
            if (in_array($newItem, $result[$i][$categoryId])) {
              continue;
            }

            $result[$i][$categoryId][] = $newItem;

            unset($questionsIdByCategoryGroupClone[$categoryId][$k]);
            $questionsIdByCategoryGroupClone[$categoryId] = array_values(
              $questionsIdByCategoryGroupClone[$categoryId]
            );

            break;
          }
        }
      }
    }

    return $result;
  }
}
