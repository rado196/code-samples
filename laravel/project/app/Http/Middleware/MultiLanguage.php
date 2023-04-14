<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;

class MultiLanguage
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $attr = $request->headers->get('X-App-Language');
    if (!$attr) {
      $attr = Language::LANGUAGE_KEY_AM;
    }

    $language = Language::query()
      ->where('country_code', $attr)
      ->first();

    $request->attributes->set('lang', $language);

    return $next($request);
  }
}
