<?php

namespace Database\Seeders\DatabaseSeeders\Common;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Language::create([
      'flag' => 'en.jpg',
      'country_code' => Language::LANGUAGE_KEY_EN,
      'country' => 'USA',
    ]);

    Language::create([
      'flag' => 'ru.jpg',
      'country_code' => Language::LANGUAGE_KEY_RU,
      'country' => 'Russian',
    ]);

    Language::create([
      'flag' => 'am.jpg',
      'country_code' => Language::LANGUAGE_KEY_AM,
      'country' => 'Armenian',
    ]);
  }
}
