<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentAppointment;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
  private function filter(Request $request, $query)
  {
    if ($request->has('startDate')) {
      $startDate = $request->get('startDate');
      $query->whereDate('date', '>=', $startDate);
    }

    if ($request->has('endDate')) {
      $endDate = $request->get('endDate');
      $query->whereBetween('date', [$startDate, $endDate]);
    }

    if ($request->has('instructor')) {
      $instructor = $request->get('instructor');
      $query->where('instructor_id', $instructor);
    }

    if ($request->has('status')) {
      $status = $request->get('status');
      $query->where('status', $status);
    }

    return $query;
  }

  /**
   * GET: /api/admin/calendar
   */
  public function getAppointments(Request $request)
  {
    $limit = 100;
    $page = $request->get('page');

    $appointments = StudentAppointment::query();

    $appointments = $this->filter($request, $appointments);

    $totalCount = $appointments->count();

    $appointments = $appointments
      ->orderBy('date')
      ->orderBy('start_time')
      ->skip($limit * $page)
      ->take($limit)
      ->get();

    return response()->json([
      'appointments' => $appointments,
      'totalCount' => $totalCount,
      'status' => 'success',
    ]);
  }
}
