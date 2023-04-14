<?php

namespace Database\Seeders\DatabaseSeeders\Development;

use App\Models\ExamGroup\ExamGroup;
use App\Models\ExamGroup\ExamGroupAnswer;
use App\Models\ExamGroup\ExamGroupAnswerTranslation;
use App\Models\ExamGroup\ExamGroupExplanation;
use App\Models\ExamGroup\ExamGroupExplanationTranslation;
use App\Models\ExamGroup\ExamGroupQuestion;
use App\Models\ExamGroup\ExamGroupQuestionTranslation;
use App\Models\ExamGroup\ExamGroupTranslation;
use App\Models\ExamGroup\QuestionCategory;
use App\Models\Language;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamGroupSeeder extends Seeder
{
  private $languages = [];

  private function fillLanguage($locale)
  {
    $language = Language::query()
      ->where('country_code', $locale)
      ->first();

    $this->languages[] = $language;
  }

  public function __construct()
  {
    $this->fillLanguage(Language::LANGUAGE_KEY_AM);
    $this->fillLanguage(Language::LANGUAGE_KEY_RU);
    $this->fillLanguage(Language::LANGUAGE_KEY_EN);
  }

  private function createExamGroup($categoryId)
  {
    $examGroup = ExamGroup::create([
      'question_category_id' => $categoryId,
    ]);

    foreach ($this->languages as $language) {
      $title = $language->country_code . ': ' . $examGroup->id;

      ExamGroupTranslation::create([
        'exam_group_id' => $examGroup->id,
        'title' => $title,
        'language_id' => $language->id,
      ]);
    }

    return $examGroup;
  }

  private function createExamGroupQuestion($examGroupId)
  {
    $examGroupQuestion = ExamGroupQuestion::create([
      'exam_group_id' => $examGroupId,
    ]);

    foreach ($this->languages as $language) {
      $title = $language->country_code . ': ' . $examGroupQuestion->id;

      ExamGroupQuestionTranslation::create([
        'exam_group_question_id' => $examGroupQuestion->id,
        'title' => $title,
        'language_id' => $language->id,
      ]);
    }

    return $examGroupQuestion;
  }

  private function createExamGroupAnswer(
    $isRightAnswer,
    $examGroupId,
    $examGroupQuestionId
  ) {
    $examGroupAnswer = ExamGroupAnswer::create([
      'is_right' => $isRightAnswer,
      'exam_group_id' => $examGroupId,
      'question_id' => $examGroupQuestionId,
    ]);

    foreach ($this->languages as $language) {
      $title = $language->country_code . ': ' . $examGroupAnswer->id;

      ExamGroupAnswerTranslation::create([
        'exam_group_answer_id' => $examGroupAnswer->id,
        'title' => $title,
        'language_id' => $language->id,
      ]);
    }

    return $examGroupAnswer;
  }

  private function createExamGroupExplanation(
    $examGroupId,
    $examGroupQuestionId
  ) {
    $examGroupExplanation = ExamGroupExplanation::create([
      'exam_group_id' => $examGroupId,
      'question_id' => $examGroupQuestionId,
    ]);

    foreach ($this->languages as $language) {
      $title = $language->country_code . ': ' . $examGroupExplanation->id;

      ExamGroupExplanationTranslation::create([
        'explanation_id' => $examGroupExplanation->id,
        'title' => $title,
        'description' => $title,
        'language_id' => $language->id,
      ]);
    }
  }

  private function create($categoryId, $questionsCount)
  {
    $examGroup = $this->createExamGroup($categoryId);
    for ($i = 0; $i < $questionsCount; ++$i) {
      $examGroupQuestion = $this->createExamGroupQuestion($examGroup->id);
      $this->createExamGroupExplanation($examGroup->id, $examGroupQuestion->id);

      for ($j = 0; $j < 4; ++$j) {
        $isRightAnswer = false;

        if ($j === 0) {
          $isRightAnswer = true;
        }

        $this->createExamGroupAnswer(
          $isRightAnswer,
          $examGroup->id,
          $examGroupQuestion->id
        );
      }
    }
  }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $categories = QuestionCategory::all();
    $counts = [147, 72, 78, 176, 135, 95, 134, 80, 126, 51];

    foreach ($categories as $index => $category) {
      $this->create($category->id, $counts[$index]);
    }
  }
}
