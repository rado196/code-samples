<?php

namespace App\Http\Controllers\Admin\ExamTests;

use App\Http\Controllers\Controller;
use App\Models\ExamTests\ExamTest;
// use App\Traits\ExamTestTraitV1;
use App\Traits\ExamTestTraitV2;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExamTestsController extends Controller
{
  // use ExamTestTraitV1;
  use ExamTestTraitV2;

  private function archiveOldExamTests()
  {
    ExamTest::query()
      // ->where('is_valid', true)
      ->update([
        'is_valid' => false,
        'deleted_at' => Carbon::now(), // soft delete
      ]);
  }

  /**
   * POST: /api/admin/exam-tests
   */
  public function generate(Request $request)
  {
    $countOfTest = $request->post('count_of_test');
    $testDuration = $request->post('test_duration');
    $maxWrongAnswers = $request->post('max_wrong_answers');
    $data = json_decode($request->post('data'));

    $this->archiveOldExamTests();
    $this->generateExamTest(
      $countOfTest,
      $testDuration,
      $maxWrongAnswers,
      $data
    );

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }
}
