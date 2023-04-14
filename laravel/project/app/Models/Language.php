<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
  use HasFactory;

  const LANGUAGE_KEY_AM = 'hy-am';
  const LANGUAGE_KEY_EN = 'en-us';
  const LANGUAGE_KEY_RU = 'ru-ru';

  public static function buildTestIndex($locale, $index)
  {
    switch ($locale) {
      case self::LANGUAGE_KEY_EN:
        return 'Test ' . $index;
      case self::LANGUAGE_KEY_AM:
        return 'Թեստ ' . $index;
      case self::LANGUAGE_KEY_RU:
        return 'Тест ' . $index;
    }
  }

  protected $table = 'languages';

  protected $fillable = ['flag', 'country_code', 'country'];
}
