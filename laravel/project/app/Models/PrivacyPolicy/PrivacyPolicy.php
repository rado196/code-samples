<?php

namespace App\Models\PrivacyPolicy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
  use HasFactory;

  protected $table = 'privacy_policies';

  protected $fillable = [];

  protected $with = ['translation', 'translations'];

  public function translation()
  {
    $language = request()->attributes->get('lang');
    return $this->hasOne(
      PrivacyPolicyTranslation::class,
      'privacy_policy_id',
      'id'
    )->where('language_id', $language->id);
  }

  public function translations()
  {
    return $this->hasMany(
      PrivacyPolicyTranslation::class,
      'privacy_policy_id',
      'id'
    );
  }
}
