<?php

namespace Database\Seeders\DatabaseSeeders\Common;

use App\Models\ExamGroup\QuestionCategory;
use App\Models\ExamGroup\QuestionCategoryTranslation;
use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionCategoriesSeeder extends Seeder
{
  private function create(array $data)
  {
    $questionCategory = QuestionCategory::create([
      'slug' => $data['slug'],
    ]);

    foreach ($data['translations'] as $translation) {
      QuestionCategoryTranslation::create([
        'question_category_id' => $questionCategory->id,
        'title' => $translation['title'],
        'language_id' => $translation['languages_id'],
      ]);
    }

    return $questionCategory;
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

    $this->create([
      'slug' => 'maneuverability_road_alignment_traffic_advantage',
      'translations' => [
        [
          'title' =>
            'Մանևրում, դասավորվածություն երթևեկելի մասում, երթևեկության առավելություն:',
          'languages_id' => $am->id,
        ],
        [
          'title' =>
            'Маневренность, выравнивание дороги, преимущество в движении.',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'Maneuverability, road alignment, traffic advantage.',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' => 'ra_law_on_ensuring_road_traffic_safety',
      'translations' => [
        [
          'title' =>
            'ՀՀ օրենք «Ճանապարհային երթևեկության անվտանգության ապահովման մասին»:',
          'languages_id' => $am->id,
        ],
        [
          'title' =>
            'Закон РА "Об обеспечении безопасности дорожного движения".',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'RA Law "On Ensuring Road Traffic Safety".',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' =>
        'malfunctions_and conditions prohibiting the operation of vehicles.',
      'translations' => [
        [
          'title' =>
            'Տրանսպորտային միջոցների շահագործումն արգելող անսարքությունները և պայմանները:',
          'languages_id' => $am->id,
        ],
        [
          'title' =>
            'Неисправности и условия, запрещающие эксплуатацию транспортных средств.',
          'languages_id' => $ru->id,
        ],
        [
          'title' =>
            'malfunctions_and_conditions_prohibiting_the_operation_of_vehicles.',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' => 'road_signs_and_road_markings',
      'translations' => [
        [
          'title' => 'Ճանապարհային նշաններ և ճանապարհային գծանշումներ:',
          'languages_id' => $am->id,
        ],
        [
          'title' => 'Дорожные знаки и дорожная разметка.',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'Road signs and road markings.',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' => 'intersection_with_signs_without_signs',
      'translations' => [
        [
          'title' => 'Խաչմերուկ (նշաններով, առանց նշանների):',
          'languages_id' => $am->id,
        ],
        [
          'title' => 'Перекресток (со знаками, без знаков).',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'Intersection (with signs, without signs).',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' => 'intersection_regulator_traffic_light',
      'translations' => [
        [
          'title' => 'Խաչմերուկ (կարգավորող, լուսացույց):',
          'languages_id' => $am->id,
        ],
        [
          'title' => 'Перекресток (регулятор, светофор).',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'Intersection (regulator, traffic light).',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' => 'road_marking_stop_station.',
      'translations' => [
        [
          'title' => 'Ճանապարհային գծանշում, կանգառ, կայանում:',
          'languages_id' => $am->id,
        ],
        [
          'title' => 'Дорожная разметка, остановка, станция.',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'Road marking, stop, station.',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' => 'speed_towing_transportation_of_people_and_cargo',
      'translations' => [
        [
          'title' => 'Արագություն, քարշակում, մարդկանց և բեռների փոխադրում:',
          'languages_id' => $am->id,
        ],
        [
          'title' => 'Скорость, буксировка, перевозка людей и грузов.',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'Speed, towing, transportation of people and cargo.',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' => 'warning_signals_special_signals_overtaking',
      'translations' => [
        [
          'title' => 'Նախազգուշացնող ազդանշաններ, հատուկ ազդանշաններ, վազանց.',
          'languages_id' => $am->id,
        ],
        [
          'title' => 'Предупреждающие сигналы, специальные сигналы, обгон.',
          'languages_id' => $ru->id,
        ],
        [
          'title' => 'Warning signals, special signals, overtaking..',
          'languages_id' => $en->id,
        ],
      ],
    ]);

    $this->create([
      'slug' =>
        'on_life_safety_of_road_users_and_first_aid_and_self-help_steps_in_case_of_accidents',
      'translations' => [
        [
          'title' =>
            'Երթևեկության մասնակիցների կյանքի անվտանգության և պատահարների դեպքում առաջին օգնության և ինքնօգնության քայլերի վերաբերյալ:',
          'languages_id' => $am->id,
        ],
        [
          'title' =>
            'О безопасности жизнедеятельности участников дорожного движения и мерах первой помощи и самопомощи при ДТП.',
          'languages_id' => $ru->id,
        ],
        [
          'title' =>
            'On life safety of road users and first aid and self-help steps in case of accidents.',
          'languages_id' => $en->id,
        ],
      ],
    ]);
  }
}
