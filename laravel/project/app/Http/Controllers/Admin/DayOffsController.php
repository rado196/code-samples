<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InstructorDayOff;
use Carbon\Carbon;

class DayOffsController extends Controller
{
  /**
   * GET: /api/admin/users/{userId}/day-offs
   */
  public function getDayOffs(Request $request, $userId)
  {
    $dayOffs = InstructorDayOff::query()
      ->where('instructor_id', $userId)
      ->whereDate('date', '>=', Carbon::today())
      ->orderBy('date')
      ->get();

    return response()->json([
      'day_offs' => $dayOffs,
    ]);
  }

  /**
   * POST: /api/admin/users/{userId}/day-offs
   */
  public function createDayOff(Request $request, $userId)
  {
    $date = $request->post('date');
    $isFullDay = $request->post('is_full_day');

    $startTime = $request->post('start_time');
    $endTime = $request->post('end_time');

    $realStartTime = $startTime;
    $realEndTime = $endTime;

    $startTime = date('H:i:s', strtotime('+1 second', strtotime($startTime)));
    $endTime = date('H:i:s', strtotime('-1 second', strtotime($endTime)));

    $date = Carbon::parse($date)->format('Y-m-d');

    $query = InstructorDayOff::query()->where('instructor_id', $userId);

    if ($isFullDay) {
      $query->whereDate('date', $date);
    } else {
      $query
        ->whereDate('date', $date)
        ->where(function ($query) use ($startTime, $endTime) {
          $query
            ->orWhereBetween('start_time', [$startTime, $endTime])
            ->orWhereBetween('end_time', [$startTime, $endTime])
            ->orWhere(function ($query) use ($startTime, $endTime) {
              $query
                ->where('start_time', '>=', $startTime)
                ->where('end_time', '<=', $endTime);
            })
            ->orWhere(function ($query) use ($startTime, $endTime) {
              $query
                ->where('start_time', '<=', $startTime)
                ->where('end_time', '>=', $endTime);
            });
        });
    }

    if ($query->exists()) {
      return response()->json(
        [
          'message' => 'error_instructor_day_off_already_exists',
          'status' => 'failed',
        ],
        500
      );
    }

    $dayOff = new InstructorDayOff();
    $dayOff->instructor_id = $userId;
    $dayOff->date = $date;
    $dayOff->is_full_day = $isFullDay;

    if (!$isFullDay) {
      $dayOff->start_time = $realStartTime;
      $dayOff->end_time = $realEndTime;
    }

    $dayOff->save();

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/users/{userId}/day-offs/{id}
   */
  public function removeDayOff(Request $request, $userId, $id)
  {
    InstructorDayOff::destroy($id);

    return response()->json([
      'status' => 'success',
    ]);
  }
}
