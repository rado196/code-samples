<?php

namespace App\Http\Controllers\Admin\ExamGroup;

use App\Http\Controllers\Controller;
use App\Models\ExamGroup\QuestionCategory;

class QuestionCategoriesController extends Controller
{
  /**
   * GET: /api/admin/question-categories
   */
  public function getCategories()
  {
    $data = QuestionCategory::query()
      ->with(['translation'])
      ->get();

    return response()->json($data);
  }
}
