<?php

namespace Database\Seeders\DatabaseSeeders\Common;

use App\Models\Language;
use App\Models\TheoreticalPartTraining\TheoreticalPartLesson;
use App\Models\TheoreticalPartTraining\TheoreticalPartLessonTranslation;
use Illuminate\Database\Seeder;

class TheoreticalPartLessonsSeeder extends Seeder
{
  private function create(array $data)
  {
    $lesson = TheoreticalPartLesson::create();

    foreach ($data['translations'] as $translation) {
      TheoreticalPartLessonTranslation::create([
        'lesson_id' => $lesson->id,
        'language_id' => $translation['languages_id'],
        'title' => $translation['title'],
      ]);
    }

    return $lesson;
  }
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
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

    for ($i = 1; $i <= 13; $i++) {
      $this->create([
        'translations' => [
          [
            'title' => 'Դաս ' . $i,
            'languages_id' => $am->id,
          ],
          [
            'title' => 'Урок ' . $i,
            'languages_id' => $ru->id,
          ],
          [
            'title' => 'Lesson ' . $i,
            'languages_id' => $en->id,
          ],
        ],
      ]);
    }
  }
}
