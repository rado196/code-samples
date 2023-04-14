<?php

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Student\ExamTests\ExamTestsController;
use App\Http\Controllers\Student\TheoreticalPartController;
use App\Http\Controllers\Student\Training\TheoreticalPartController as TrainingController;
use App\Http\Controllers\Student\Training\DrivingCourseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/student'], function () {
  Route::get('/count', [UsersController::class, 'getStudentsCount']);

  Route::group(['prefix' => '/exam-tests'], function () {
    Route::get('/', [ExamTestsController::class, 'getExamTests']);
    Route::get('/{id}', [ExamTestsController::class, 'getExamTest']);
    Route::post('/{id}', [ExamTestsController::class, 'generateNewExamTest']);

    Route::group(['prefix' => '/{examTestUniqueId}/answer'], function () {
      Route::post('/', [ExamTestsController::class, 'chooseAnswer']);
    });

    Route::group(['prefix' => '/{examTestUniqueId}/expired'], function () {
      Route::post('/', [ExamTestsController::class, 'expiredExamTest']);
    });
  });

  Route::group(['prefix' => '/theoretical-part'], function () {
    Route::get('/traffic-rules', [
      TheoreticalPartController::class,
      'getTrafficRules',
    ]);
    Route::get('/road-signs', [
      TheoreticalPartController::class,
      'getRoadSigns',
    ]);
    Route::get('/road-markings', [
      TheoreticalPartController::class,
      'getRoadMarkings',
    ]);
    Route::get('/vehicle-sign', [
      TheoreticalPartController::class,
      'getVehicleSign',
    ]);
    Route::get('/malfunction-list', [
      TheoreticalPartController::class,
      'getMalfunctionList',
    ]);
    Route::get('/road-safety-laws', [
      TheoreticalPartController::class,
      'getRoadSafetyLaws',
    ]);
  });

  Route::group(['prefix' => '/trainings'], function () {
    Route::group(['prefix' => '/driving-course'], function () {
      Route::group(['prefix' => '/appointments'], function () {
        Route::get('/', [DrivingCourseController::class, 'getAppointments']);
        Route::post('/', [DrivingCourseController::class, 'createAppointment']);
        Route::patch('/{id}', [
          DrivingCourseController::class,
          'editAppointment',
        ]);
        Route::delete('/{id}', [
          DrivingCourseController::class,
          'cancelAppointment',
        ]);

        Route::group(['prefix' => '/booked-times'], function () {
          Route::get('/', [
            DrivingCourseController::class,
            'getAppointmentsBookedTimes',
          ]);
        });

        Route::group(['prefix' => '/day-offs'], function () {
          Route::get('/', [
            DrivingCourseController::class,
            'getAppointmentsDayOffs',
          ]);
        });
      });
    });

    Route::group(['prefix' => '/theoretical-part'], function () {
      Route::get('/my-trainings', [
        TrainingController::class,
        'getTheoreticalPartLessonMyTraining',
      ]);

      Route::group(['prefix' => '/lessons'], function () {
        Route::get('/', [
          TrainingController::class,
          'getTheoreticalPartLessons',
        ]);
        Route::get('/{trainingId}', [
          TrainingController::class,
          'getTheoreticalPartLessonTraining',
        ]);
        Route::post('/buy', [TrainingController::class, 'buyCourse']);
      });
    });
  });
});
