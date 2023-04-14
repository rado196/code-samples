<?php

namespace App\Models\PrivacyPolicy;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicyTranslation extends Model
{
  use HasFactory;

  protected $table = 'privacy_policy_translations';

  protected $fillable = ['privacy_policy_id', 'language_id', 'content'];

  protected $with = ['language'];

  public function language()
  {
    return $this->hasOne(Language::class, 'id', 'language_id');
  }
}
