<?php

use App\Http\Controllers\Instructor\InstructorsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/instructors'], function () {
  Route::get('/', [InstructorsController::class, 'getInstructors']);
  Route::get('/count', [InstructorsController::class, 'getInstructorCount']);
  Route::get('/{id}', [InstructorsController::class, 'getInstructor']);
  Route::get('/{id}/ratings', [
    InstructorsController::class,
    'getInstructorRatings',
  ]);
  Route::get('/student/with-hidden', [
    InstructorsController::class,
    'getInstructorWithHidden',
  ]);
});
