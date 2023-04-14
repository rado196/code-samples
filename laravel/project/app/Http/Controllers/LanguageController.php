<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Language;

class LanguageController extends Controller
{
  /**
   * GET: /api/admin/languages
   */
  public function getLanguages()
  {
    $languages = Language::all();

    return response()->json([
      'languages' => $languages,
    ]);
  }
}
