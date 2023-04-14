<?php

namespace App\Http\Controllers\Student\Training;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CreateAppointmentRequest;
use App\Models\InstructorDayOff;
use App\Models\InstructorWorkDetail;
use App\Models\StudentAppointment;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DrivingCourseController extends Controller
{
  private function checkTimeRange(
    Builder $query,
    string $date,
    string $startTime,
    string $endTime,
    bool $offsetTime,
    ?callable $appendQueryChild = null,
    ?callable $appendQueryRoot = null
  ) {
    if ($offsetTime) {
      $startTime = date('H:i:s', strtotime('+1 second', strtotime($startTime)));
      $endTime = date('H:i:s', strtotime('-1 second', strtotime($endTime)));
    }

    $query
      // ->where('instructor_id', $instructorId)
      ->whereDate('date', $date)
      ->where(function ($query) use ($appendQueryChild, $startTime, $endTime) {
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

        if (is_callable($appendQueryChild)) {
          $appendQueryChild($query);
        }
      });

    if (is_callable($appendQueryRoot)) {
      $appendQueryRoot($query);
    }

    return $query->exists();
  }

  private function findAppointment($instructorId, $date, $startTime, $endTime)
  {
    $query = StudentAppointment::query()->whereIn('status', [
      StudentAppointment::STATUS_BOOKED,
      StudentAppointment::STATUS_COMPLETED,
      StudentAppointment::STATUS_PENDING,
    ]);

    return $this->checkTimeRange(
      $query,
      $date,
      $startTime,
      $endTime,
      true,
      null,
      function ($query) use ($instructorId) {
        $query->where('instructor_id', $instructorId);
      }
    );
  }

  private function findInstructorDayOff(
    $instructorId,
    $date,
    $startTime,
    $endTime
  ) {
    $query = InstructorDayOff::query();
    return $this->checkTimeRange(
      $query,
      $date,
      $startTime,
      $endTime,
      true,
      function ($query) {
        $query->orWhere('is_full_day', true);
      },
      function ($query) use ($instructorId) {
        $query->where('instructor_id', $instructorId);
      }
    );
  }

  private function isOutOfWorkingPeriod(
    $instructorId,
    $date,
    $startTime,
    $endTime
  ) {
    $weekDays = [
      'sunday',
      'monday',
      'tuesday',
      'wednesday',
      'thursday',
      'friday',
      'saturday',
    ];

    $dayOfWeek = Carbon::parse($date)->dayOfWeek;

    $fieldOfStartTime = $weekDays[$dayOfWeek] . '_start_time';
    $fieldOfEndTime = $weekDays[$dayOfWeek] . '_end_time';

    $available = InstructorWorkDetail::query()
      ->where('instructor_id', $instructorId)
      ->where($weekDays[$dayOfWeek], true)
      ->where($fieldOfStartTime, '<=', $startTime)
      ->where($fieldOfEndTime, '>=', $endTime)
      ->exists();

    return !$available;
  }

  /**
   * GET: /api/student/trainings/driving-course/appointments
   */
  public function getAppointments(Request $request)
  {
    $instructorId = $request->get('instructor_id');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    $appointments = StudentAppointment::query()
      ->where('student_id', Auth::id())
      ->where('instructor_id', $instructorId)
      ->whereBetween('date', [$startDate, $endDate])
      ->whereNotIn('status', [StudentAppointment::STATUS_EXPIRED])
      ->get();

    return response()->json([
      'appointments' => $appointments,
    ]);
  }

  /**
   * GET: /api/student/trainings/driving-course/appointments/booked-times
   */
  public function getAppointmentsBookedTimes(Request $request)
  {
    $instructorId = $request->get('instructor_id');
    $date = $request->get('date');

    $appointments = StudentAppointment::query()
      ->where('instructor_id', $instructorId)
      ->whereDate('date', $date)
      ->whereNotIn('status', [
        StudentAppointment::STATUS_EXPIRED,
        StudentAppointment::STATUS_CANCELED,
      ])
      ->pluck('start_time', 'end_time');

    return response()->json([
      'bookedTimes' => $appointments,
    ]);
  }

  /**
   * GET: /api/student/trainings/driving-course/appointments/day-offs
   */
  public function getAppointmentsDayOffs(Request $request)
  {
    $instructorId = $request->get('instructor_id');
    $date = $request->get('date');

    $instructorDayOffs = InstructorDayOff::query()
      ->where('instructor_id', $instructorId)
      ->whereDate('date', $date)
      ->pluck('start_time', 'end_time');

    return response()->json([
      'dayOffs' => $instructorDayOffs,
    ]);
  }

  /**
   * POST: /api/student/trainings/driving-course/appointments
   */
  public function createAppointment(CreateAppointmentRequest $request)
  {
    $authId = Auth::id();
    // $studentWallet = Wallet::query()
    //   ->where('user_id', $authId)
    //   ->first();

    $instructorId = $request->post('instructor_id');
    $instructorWorkDetails = InstructorWorkDetail::query()
      ->where('instructor_id', $instructorId)
      ->first();

    $date = $request->post('date');
    $startTime = $request->post('start_time');
    $endTime = $request->post('end_time');
    $duration = $request->post('duration'); // minutes
    $price = $request->post('price');

    $duration = $duration / 60; // hours

    $date = Carbon::parse($date)->format('Y-m-d');

    if (
      intval($price / $duration) !==
      $instructorWorkDetails->driving_course_hourly_price
    ) {
      return response()->json(
        [
          'message' => 'appointment_creation_error_invalid_credentials',
          'status' => 'failed',
        ],
        400
      );
    }

    $isOutOfWorkingPeriod = $this->isOutOfWorkingPeriod(
      $instructorId,
      $date,
      $startTime,
      $endTime
    );

    if ($isOutOfWorkingPeriod) {
      return response()->json(
        [
          'message' =>
            'calendar@appointment_creation_error_selected_time_is_not_matched_instructor_working_period',
          'status' => 'failed',
        ],
        400
      );
    }

    $isFindInstructorDayOff = $this->findInstructorDayOff(
      $instructorId,
      $date,
      $startTime,
      $endTime
    );

    if ($isFindInstructorDayOff) {
      return response()->json(
        [
          'message' =>
            'calendar@appointment_creation_error_appointment_is_matched_instructor_day_off',
          'status' => 'failed',
        ],
        400
      );
    }

    $isExistsAppointment = $this->findAppointment(
      $instructorId,
      $date,
      $startTime,
      $endTime
    );

    if ($isExistsAppointment) {
      return response()->json(
        [
          'message' =>
            'calendar@appointment_creation_error_time_interval_already_booked',
          'status' => 'failed',
        ],
        400
      );
    }

    $studentAppointment = new StudentAppointment();
    $studentAppointment->student_id = $authId;
    $studentAppointment->instructor_id = $instructorId;
    $studentAppointment->date = $date;
    $studentAppointment->start_time = $startTime;
    $studentAppointment->end_time = $endTime;
    $studentAppointment->duration = $duration;
    $studentAppointment->price = $price;

    $studentAppointment->status = StudentAppointment::STATUS_PENDING;

    // if ($studentWallet->balance >= $price) {
    //   $studentWallet->decrement('balance', $price);
    //   $studentAppointment->status = StudentAppointment::STATUS_BOOKED;
    // } else {
    //   $studentAppointment->status = StudentAppointment::STATUS_PENDING;
    // }

    $studentAppointment->save();

    return response()->json([
      'action_status' => $studentAppointment->status,
      'status' => 'success',
    ]);
  }

  /**
   * PATCH: /api/student/trainings/driving-course/appointments/{id}
   */
  public function editAppointment(Request $request, $id)
  {
    if ($request->has('action')) {
      $authId = Auth::id();

      $studentWallet = Wallet::query()
        ->where('user_id', $authId)
        ->first();

      $studentAppointment = StudentAppointment::find($id);

      if ('appointment_confirmation' === $request->post('action')) {
        if ($studentWallet->balance >= $studentAppointment->price) {
          $studentWallet->decrement('balance', $studentAppointment->price);
          $studentAppointment->status = StudentAppointment::STATUS_BOOKED;
        } else {
          $studentAppointment->status = StudentAppointment::STATUS_PENDING;
        }

        $studentAppointment->save();

        return response()->json([
          'action_status' => $studentAppointment->status,
          'status' => 'success',
        ]);
      }
    }

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/student/trainings/driving-course/appointments/{id}
   */

  public function cancelAppointment(Request $request, $id)
  {
    $studentId = Auth::id();
    $studentWallet = Wallet::query()
      ->where('user_id', $studentId)
      ->first();

    $studentAppointment = StudentAppointment::find($id);

    if ($studentAppointment->status === StudentAppointment::STATUS_BOOKED) {
      $studentWallet->increment('balance', $studentAppointment->price);
    }

    $studentAppointment->status = StudentAppointment::STATUS_CANCELED;
    $studentAppointment->save();

    return response()->json([
      'wallet' => $studentWallet,
      'status' => 'success',
    ]);
  }
}
