<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Payment\AmeriaBankController;
use App\Http\Controllers\Payment\ArcaController;
use App\Http\Controllers\Payment\EasyPayController;
use App\Http\Controllers\Payment\IDramController;
use App\Http\Controllers\Payment\PaymentsController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Profile\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the 'api' middleware group. Enjoy building your API!
|
*/

if (!defined('MIDDLEWARE_GROUP_AUTH')) {
  define('MIDDLEWARE_GROUP_AUTH', ['jwt', 'auth:api']);
}

require_once __DIR__ . '/resource.php';
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/student.php';
require_once __DIR__ . '/instructor.php';

Route::get('/ping', function () {
  return response()->json([
    'message' => 'pong',
    'datetime' => date('d.m.Y H:i:s'),
    'timezone' => config('app.timezone'),
  ]);
});

// Languages
Route::group(['prefix' => '/languages'], function () {
  Route::get('/', [LanguageController::class, 'getLanguages']);
});

Route::group(['prefix' => '/users'], function () {
  Route::post('/', [RegisterController::class, 'register']);
  Route::put('/verification', [VerifyController::class, 'verify']);

  Route::group(['prefix' => '/login'], function () {
    Route::get('/state', [LoginController::class, 'checkIsRegistered']);

    Route::post('/', [LoginController::class, 'login']);
    Route::post('/apple/{id}', [LoginController::class, 'appleLogin']);
    Route::post('/facebook/{id}', [LoginController::class, 'facebookLogin']);
    Route::post('/google/{id}', [LoginController::class, 'googleLogin']);
  });

  Route::post('/logout', [LoginController::class, 'logout'])->middleware(
    MIDDLEWARE_GROUP_AUTH
  );

  Route::group(['prefix' => '/password'], function () {
    Route::put('/', [ChangePasswordController::class, 'change'])->middleware(
      MIDDLEWARE_GROUP_AUTH
    );

    Route::group(['prefix' => '/recovery'], function () {
      Route::post('/', [PasswordResetController::class, 'create']);
      Route::get('/', [PasswordResetController::class, 'find']);
      Route::put('/', [PasswordResetController::class, 'reset']);
    });
  });

  Route::group(
    [
      'middleware' => MIDDLEWARE_GROUP_AUTH,
    ],
    function () {
      Route::get('/me', [UserController::class, 'getMe']);

      Route::group(['prefix' => '/profile'], function () {
        Route::put('/', [ProfileController::class, 'edit']);
        Route::patch('/', [ProfileController::class, 'update']);
      });
    }
  );
});

Route::group(
  [
    'middleware' => ['payment_logger'],
    'prefix' => '/payments',
  ],
  function () {
    Route::group(['prefix' => '/ameria-bank'], function () {
      Route::post('/initialize', [AmeriaBankController::class, 'initialize']);
      Route::post('/confirmation', [AmeriaBankController::class, 'confirm']);
    });

    Route::group(['prefix' => '/arca'], function () {
      Route::post('/initialize', [ArcaController::class, 'initialize']);
      Route::post('/confirmation', [ArcaController::class, 'confirm']);
    });

    Route::group(['prefix' => '/idram'], function () {
      Route::post('/initialize', [IDramController::class, 'initialize']);
      Route::post('/response-confirmation', [
        IDramController::class,
        'checking',
      ]);
      Route::post('/confirmation', [IDramController::class, 'confirm']);
    });

    Route::group(['prefix' => '/easy-pay'], function () {
      Route::post('/check', [EasyPayController::class, 'check']);
      Route::post('/payment', [EasyPayController::class, 'payment']);
    });

    Route::group(['prefix' => '/history'], function () {
      Route::get('/', [PaymentsController::class, 'getHistories']);
    });
  }
);

Route::group(['prefix' => '/comments'], function () {
  Route::get('/', [CommentsController::class, 'getComments']);
  Route::get('/{id}', [CommentsController::class, 'getComment']);
  Route::post('/', [CommentsController::class, 'make']);
});
