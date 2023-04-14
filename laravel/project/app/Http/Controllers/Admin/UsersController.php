<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\InstructorWorkDetail;
use App\Models\HiddenInstructorStudent;
use App\Models\InstructorInfo\InstructorInfo;
use App\Models\InstructorInfo\InstructorInfoTranslation;
use Carbon\Carbon;

class UsersController extends Controller
{
  private function userFilter(Request $request, $query)
  {
    if ($request->has('role')) {
      $role = $request->get('role');
      $roleList = [];

      switch ($role) {
        case 'all':
          $roleList = [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR];
          break;

        case User::ROLE_STUDENT:
          $roleList = [User::ROLE_STUDENT];
          break;

        case User::ROLE_INSTRUCTOR:
          $roleList = [User::ROLE_INSTRUCTOR];
          break;
      }
      $query->whereIn('role', $roleList);
    }

    if ($request->has('q')) {
      $q = $request->get('q');

      $q = '%' . $q . '%';

      $query->where(function ($query) use ($q) {
        $query
          ->where('first_name', 'like', $q)
          ->orWhere('last_name', 'like', $q)
          ->orWhere('email', 'like', $q)
          ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', $q);
      });
    }

    return $query;
  }

  private function createTranslations(Request $request, $instructorInfoId)
  {
    $info = json_decode($request->post('instructor_info'));

    foreach ($info as $key => $singleData) {
      InstructorInfoTranslation::create([
        'instructor_info_id' => $instructorInfoId,
        'language_id' => $singleData->lang_id,
        'description' => $singleData->description,
      ]);
    }
  }

  private function cleanupTranslations($instructorInfoId)
  {
    InstructorInfoTranslation::query()
      ->where('instructor_info_id', $instructorInfoId)
      ->delete();
  }

  /**
   * GET: /api/admin/users
   */
  public function getList(Request $request)
  {
    $limit = 50;
    $page = $request->get('page');

    $query = User::query()->with('wallet');

    $query = $this->userFilter($request, $query);

    $totalCount = $query->count();

    $listOfUsers = $query
      ->skip($limit * $page)
      ->take($limit)
      ->get();

    return response()->json([
      'list' => $listOfUsers,
      'totalCount' => $totalCount,
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/admin/students
   */
  public function getStudentList()
  {
    $listOfStudents = User::query()
      ->where('role', User::ROLE_STUDENT)
      ->get();

    return response()->json([
      'list' => $listOfStudents,
    ]);
  }

  /**
   * GET: /api/admin/students/with-hidden-instructor/{id}
   */
  public function getStudentListWithHiddenInstructor(
    Request $request,
    $instructorId
  ) {
    $listOfStudents = User::query()
      ->with('student_hidden_instructors', function ($query) use (
        $instructorId
      ) {
        $query->where('instructor_id', $instructorId);
      })
      ->where('role', User::ROLE_STUDENT)
      ->get();

    return response()->json([
      'list' => $listOfStudents,
    ]);
  }

  /**
   * GET: /api/admin/users/{id}
   */
  public function get($id)
  {
    $user = User::query()
      ->with([
        'instructor_work_detail',
        'hidden_instructor_students',
        'student_hidden_instructors',
        'instructor_info',
      ])
      ->whereId($id)
      ->first();

    return response()->json([
      'user' => $user,
    ]);
  }

  /**
   * POST: /api/admin/users
   */
  public function add(Request $request)
  {
    $firstName = $request->post('first_name');
    $lastName = $request->post('last_name');
    $drivingCourseHourlyPrice = $request->post('driving_course_hourly_price');
    $hidden = $request->post('hidden');
    $selectedStudents = $request->post('selected_students');
    $email = $request->post('email');
    $phone = $request->post('phone');
    $password = $request->post('password');
    $gender = $request->post('gender');

    $user = new User();
    $user->first_name = $firstName;
    $user->last_name = $lastName;
    $user->role = User::ROLE_INSTRUCTOR;
    $user->email = $email;
    $user->gender = $gender;
    $user->phone = $phone;
    $user->password = Hash::make($password);
    $user->verified_at = Carbon::now();
    $user->save();

    $monday = json_decode($request->post('monday'));
    $tuesday = json_decode($request->post('tuesday'));
    $wednesday = json_decode($request->post('wednesday'));
    $thursday = json_decode($request->post('thursday'));
    $friday = json_decode($request->post('friday'));
    $saturday = json_decode($request->post('saturday'));
    $sunday = json_decode($request->post('sunday'));

    InstructorWorkDetail::create([
      'instructor_id' => $user->id,
      'driving_course_hourly_price' => $drivingCourseHourlyPrice,
      'hidden' => $hidden === 'true',
      'monday' => $monday->checked,
      'tuesday' => $tuesday->checked,
      'wednesday' => $wednesday->checked,
      'thursday' => $thursday->checked,
      'friday' => $friday->checked,
      'saturday' => $saturday->checked,
      'sunday' => $sunday->checked,
      'monday_start_time' => $monday->startTime,
      'monday_end_time' => $monday->endTime,
      'tuesday_start_time' => $tuesday->startTime,
      'tuesday_end_time' => $tuesday->endTime,
      'wednesday_start_time' => $wednesday->startTime,
      'wednesday_end_time' => $wednesday->endTime,
      'thursday_start_time' => $thursday->startTime,
      'thursday_end_time' => $thursday->endTime,
      'friday_start_time' => $friday->startTime,
      'friday_end_time' => $friday->endTime,
      'saturday_start_time' => $saturday->startTime,
      'saturday_end_time' => $saturday->endTime,
      'sunday_start_time' => $sunday->startTime,
      'sunday_end_time' => $sunday->endTime,
    ]);

    if ($hidden === 'true') {
      $selectedStudents = json_decode($selectedStudents);
      foreach ($selectedStudents as $item) {
        HiddenInstructorStudent::create([
          'instructor_id' => $user->id,
          'student_id' => $item->value,
        ]);
      }
    }

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * PUT: /api/admin/users/{id}
   */
  public function edit(Request $request, $id)
  {
    $teacherWorkDetailId = $request->post('instructor_work_detail_id');
    $drivingCourseHourlyPrice = $request->post('driving_course_hourly_price');
    $hidden = $request->post('hidden');
    $selectedStudents = $request->post('selected_students');
    $firstName = $request->post('first_name');
    $lastName = $request->post('last_name');
    $email = $request->post('email');
    $phone = $request->post('phone');
    $password = $request->post('password');
    $gender = $request->post('gender');
    $role = $request->post('role');

    $user = User::find($id);
    $user->first_name = $firstName;
    $user->last_name = $lastName;
    $user->role = $role;
    $user->email = $email;
    $user->gender = $gender;
    $user->phone = $phone;

    if (!is_null($password)) {
      $user->password = Hash::make($password);
    }

    $user->verified_at = Carbon::now();
    $user->save();

    if (User::ROLE_INSTRUCTOR === $role) {
      $monday = json_decode($request->post('monday'));
      $tuesday = json_decode($request->post('tuesday'));
      $wednesday = json_decode($request->post('wednesday'));
      $thursday = json_decode($request->post('thursday'));
      $friday = json_decode($request->post('friday'));
      $saturday = json_decode($request->post('saturday'));
      $sunday = json_decode($request->post('sunday'));

      $teacherWorkDetail = InstructorWorkDetail::find($teacherWorkDetailId);
      $teacherWorkDetail->driving_course_hourly_price = $drivingCourseHourlyPrice;
      $teacherWorkDetail->hidden = $hidden === 'true';
      $teacherWorkDetail->monday = $monday->checked;
      $teacherWorkDetail->tuesday = $tuesday->checked;
      $teacherWorkDetail->wednesday = $wednesday->checked;
      $teacherWorkDetail->thursday = $thursday->checked;
      $teacherWorkDetail->friday = $friday->checked;
      $teacherWorkDetail->saturday = $saturday->checked;
      $teacherWorkDetail->sunday = $sunday->checked;
      $teacherWorkDetail->monday_start_time = $monday->startTime;
      $teacherWorkDetail->monday_end_time = $monday->endTime;
      $teacherWorkDetail->tuesday_start_time = $tuesday->startTime;
      $teacherWorkDetail->tuesday_end_time = $tuesday->endTime;
      $teacherWorkDetail->wednesday_start_time = $wednesday->startTime;
      $teacherWorkDetail->wednesday_end_time = $wednesday->endTime;
      $teacherWorkDetail->thursday_start_time = $thursday->startTime;
      $teacherWorkDetail->thursday_end_time = $thursday->endTime;
      $teacherWorkDetail->friday_start_time = $friday->startTime;
      $teacherWorkDetail->friday_end_time = $friday->endTime;
      $teacherWorkDetail->saturday_start_time = $saturday->startTime;
      $teacherWorkDetail->saturday_end_time = $saturday->endTime;
      $teacherWorkDetail->sunday_start_time = $sunday->startTime;
      $teacherWorkDetail->sunday_end_time = $sunday->endTime;
      $teacherWorkDetail->save();

      $instructorInfo = InstructorInfo::query()
        ->where('instructor_id', $id)
        ->first();

      if (is_null($instructorInfo)) {
        $instructorInfo = InstructorInfo::create([
          'instructor_id' => $id,
        ]);
      }

      $this->cleanupTranslations($instructorInfo->id);
      $this->createTranslations($request, $instructorInfo->id);

      HiddenInstructorStudent::query()
        ->where('instructor_id', $user->id)
        ->delete();

      if ($hidden === 'true') {
        $selectedStudents = json_decode($selectedStudents);
        foreach ($selectedStudents as $item) {
          HiddenInstructorStudent::create([
            'instructor_id' => $user->id,
            'student_id' => $item->value,
          ]);
        }
      }
    }

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * PATCH: /api/admin/users/{id}
   */
  public function update(Request $request, $id)
  {
    $studentWallet = null;

    if ($request->has('ballance')) {
      $ballance = $request->post('ballance');

      $studentWallet = Wallet::query()
        ->where('user_id', $id)
        ->first();

      $studentWallet->increment('balance', $ballance);
    }

    return response()->json([
      'wallet' => $studentWallet,
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/users/{id}
   */
  public function remove($id)
  {
    User::destroy($id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/admin/users/count
   */
  public function getStudentsCount(Request $request)
  {
    $students = User::query()
      ->where('role', User::ROLE_STUDENT)
      ->count();

    return response()->json([
      'count' => $students,
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/admin/transactions/total
   */
  public function getTransactionsTotal(Request $request)
  {
    $dateStart = Carbon::now()
      ->startOfMonth()
      ->format('Y-m-d H:i:s');
    $dateEnd = Carbon::now()
      ->endOfMonth()
      ->format('Y-m-d H:i:s');

    $total = Payment::query()
      ->where('status', Payment::STATUS_SUCCESS)
      ->whereBetween('created_at', [$dateStart, $dateEnd])
      ->sum('amount');

    return response()->json([
      'total' => $total,
      'status' => 'success',
    ]);
  }
}
