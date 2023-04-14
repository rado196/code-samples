<?php

use App\Http\Controllers\Admin\CalendarController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DayOffsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ExamGroup\ExamGroupController;
use App\Http\Controllers\Admin\ExamGroup\ExamGroupQuestionsController;
use App\Http\Controllers\Admin\ExamGroup\QuestionCategoriesController;
use App\Http\Controllers\Admin\ExamGroup\AnswersController;
use App\Http\Controllers\Admin\ExamTests\ExamTestsController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MalfunctionList\MalfunctionListArticleController;
use App\Http\Controllers\Admin\MalfunctionList\MalfunctionListController;
use App\Http\Controllers\Admin\PaymentsListController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\RoadMarking\RoadMarkingArticleController;
use App\Http\Controllers\Admin\RoadMarking\RoadMarkingController;
use App\Http\Controllers\Admin\RoadSafetyLaws\RoadSafetyLawArticlesController;
use App\Http\Controllers\Admin\RoadSafetyLaws\RoadSafetyLawsController;
use App\Http\Controllers\Admin\RoadSign\RoadSignArticleController;
use App\Http\Controllers\Admin\RoadSign\RoadSignController;
use App\Http\Controllers\Admin\TheoreticalPartLesson\LessonsController;
use App\Http\Controllers\Admin\TheoreticalPartLesson\TrainingsController;
use App\Http\Controllers\Admin\TheoreticalPartLesson\VideosController;
use App\Http\Controllers\Admin\TrafficRule\TrafficRuleArticleController;
use App\Http\Controllers\Admin\TrafficRule\TrafficRuleController;
use App\Http\Controllers\Admin\VehicleSignController;

Route::group(
  [
    'prefix' => '/admin',
  ],
  function () {
    Route::post('/logout', [LoginController::class, 'logout'])->middleware(
      MIDDLEWARE_GROUP_AUTH
    );

    Route::post('/login', [LoginController::class, 'login']);

    // Question categories
    Route::group(['prefix' => '/question-categories'], function () {
      Route::get('/', [QuestionCategoriesController::class, 'getCategories']);
    });

    // Users
    Route::group(['prefix' => '/users'], function () {
      Route::get('/', [UsersController::class, 'getList']);
      Route::post('/', [UsersController::class, 'add']);
      Route::get('/{id}', [UsersController::class, 'get']);
      Route::put('/{id}', [UsersController::class, 'edit']);
      Route::patch('/{id}', [UsersController::class, 'update']);
      Route::delete('/{id}', [UsersController::class, 'remove']);

      Route::group(['prefix' => '/{userId}/day-offs'], function () {
        Route::get('/', [DayOffsController::class, 'getDayOffs']);
        Route::post('/', [DayOffsController::class, 'createDayOff']);
        Route::delete('/{id}', [DayOffsController::class, 'removeDayOff']);
      });
    });

    Route::group(['prefix' => '/students'], function () {
      Route::get('/', [UsersController::class, 'getStudentList']);
      Route::get('/with-hidden-instructor/{id}', [
        UsersController::class,
        'getStudentListWithHiddenInstructor',
      ]);
    });

    // Exam Tests
    Route::group(['prefix' => '/exam-tests'], function () {
      Route::post('/', [ExamTestsController::class, 'generate']);
    });

    // Exam Groups
    Route::group(['prefix' => '/exam-groups'], function () {
      Route::get('/', [ExamGroupController::class, 'getExamGroups']);
      Route::post('/', [ExamGroupController::class, 'addExamGroup']);
      Route::get('/{id}', [ExamGroupController::class, 'getExamGroup']);
      Route::put('/{id}', [ExamGroupController::class, 'updateExamGroup']);
      Route::delete('/{id}', [ExamGroupController::class, 'deleteExamGroup']);

      // Exam Group Questions
      Route::group(['prefix' => '/{examGroupId}/questions'], function () {
        Route::get('/', [ExamGroupQuestionsController::class, 'getQuestions']);
        Route::post('/', [ExamGroupQuestionsController::class, 'addQuestion']);
        Route::get('/{id}', [
          ExamGroupQuestionsController::class,
          'getQuestion',
        ]);
        Route::put('/{id}', [
          ExamGroupQuestionsController::class,
          'updateQuestion',
        ]);
        Route::delete('/{id}', [
          ExamGroupQuestionsController::class,
          'deleteQuestion',
        ]);

        // Exam Group Answers
        Route::group(
          ['prefix' => '/{examGroupQuestionId}/answers'],
          function () {
            Route::get('/', [AnswersController::class, 'getAnswers']);
            Route::post('/', [AnswersController::class, 'addAnswer']);
            Route::get('/{id}', [AnswersController::class, 'getAnswer']);
            Route::put('/{id}', [AnswersController::class, 'updateAnswer']);
            Route::put('/{id}/right-answer', [
              AnswersController::class,
              'setRightAnswer',
            ]);
            Route::delete('/{id}', [AnswersController::class, 'deleteAnswer']);
          }
        );
      });
    });

    // Traffic Rule Articles
    Route::group(['prefix' => '/traffic-rule-articles'], function () {
      Route::get('/', [
        TrafficRuleArticleController::class,
        'getTrafficRuleArticles',
      ]);
      Route::post('/', [
        TrafficRuleArticleController::class,
        'addTrafficRuleArticle',
      ]);
      Route::get('/{id}', [
        TrafficRuleArticleController::class,
        'getTrafficRuleArticle',
      ]);
      Route::put('/{id}', [
        TrafficRuleArticleController::class,
        'updateTrafficRuleArticle',
      ]);
      Route::delete('/{id}', [
        TrafficRuleArticleController::class,
        'deleteTrafficRuleArticle',
      ]);

      // Traffic Rules
      Route::group(['prefix' => '/{articleId}/traffic-rules'], function () {
        Route::get('/', [TrafficRuleController::class, 'getTrafficRules']);
        Route::post('/', [TrafficRuleController::class, 'addTrafficRule']);
        Route::post('/upload', [TrafficRuleController::class, 'upload']);
        Route::get('/{id}', [TrafficRuleController::class, 'getTrafficRule']);
        Route::put('/{id}', [
          TrafficRuleController::class,
          'updateTrafficRule',
        ]);
        Route::delete('/{id}', [
          TrafficRuleController::class,
          'deleteTrafficRule',
        ]);
      });
    });

    // Road sign Articles
    Route::group(['prefix' => '/road-sign-articles'], function () {
      Route::get('/', [
        RoadSignArticleController::class,
        'getRoadSignArticles',
      ]);
      Route::post('/', [
        RoadSignArticleController::class,
        'addRoadSignArticle',
      ]);
      Route::get('/{id}', [
        RoadSignArticleController::class,
        'getRoadSignArticle',
      ]);
      Route::put('/{id}', [
        RoadSignArticleController::class,
        'updateRoadSignArticle',
      ]);
      Route::delete('/{id}', [
        RoadSignArticleController::class,
        'deleteRoadSignArticle',
      ]);

      // Road sign
      Route::group(['prefix' => '/{articleId}/road-signs'], function () {
        Route::get('/', [RoadSignController::class, 'getRoadSigns']);
        Route::post('/', [RoadSignController::class, 'addRoadSign']);
        Route::post('/upload', [RoadSignController::class, 'upload']);
        Route::get('/{id}', [RoadSignController::class, 'getRoadSign']);
        Route::put('/{id}', [RoadSignController::class, 'updateRoadSign']);
        Route::delete('/{id}', [RoadSignController::class, 'deleteRoadSign']);
      });
    });

    // Road marking Articles
    Route::group(['prefix' => '/road-marking-articles'], function () {
      Route::get('/', [
        RoadMarkingArticleController::class,
        'getRoadMarkingArticles',
      ]);
      Route::post('/', [
        RoadMarkingArticleController::class,
        'addRoadMarkingArticle',
      ]);
      Route::get('/{id}', [
        RoadMarkingArticleController::class,
        'getRoadMarkingArticle',
      ]);
      Route::put('/{id}', [
        RoadMarkingArticleController::class,
        'updateRoadMarkingArticle',
      ]);
      Route::delete('/{id}', [
        RoadMarkingArticleController::class,
        'deleteRoadMarkingArticle',
      ]);

      // Road marking
      Route::group(['prefix' => '/{articleId}/road-markings'], function () {
        Route::get('/', [RoadMarkingController::class, 'getRoadMarkings']);
        Route::post('/', [RoadMarkingController::class, 'addRoadMarking']);
        Route::post('/upload', [RoadMarkingController::class, 'upload']);
        Route::get('/{id}', [RoadMarkingController::class, 'getRoadMarking']);
        Route::put('/{id}', [
          RoadMarkingController::class,
          'updateRoadMarking',
        ]);
        Route::delete('/{id}', [
          RoadMarkingController::class,
          'deleteRoadMarking',
        ]);
      });
    });

    // Vehicle signs
    Route::group(['prefix' => '/vehicle-signs'], function () {
      Route::get('/', [VehicleSignController::class, 'getVehicleSigns']);
      Route::post('/', [VehicleSignController::class, 'addVehicleSign']);
      Route::post('/upload', [VehicleSignController::class, 'upload']);
      Route::get('/{id}', [VehicleSignController::class, 'getVehicleSign']);
      Route::put('/{id}', [VehicleSignController::class, 'updateVehicleSign']);
      Route::delete('/{id}', [
        VehicleSignController::class,
        'deleteVehicleSign',
      ]);
    });

    // Malfunction list articles
    Route::group(['prefix' => '/malfunction-list-articles'], function () {
      Route::get('/', [
        MalfunctionListArticleController::class,
        'getMalfunctionListArticles',
      ]);
      Route::post('/', [
        MalfunctionListArticleController::class,
        'addMalfunctionListArticle',
      ]);
      Route::get('/{id}', [
        MalfunctionListArticleController::class,
        'getMalfunctionListArticle',
      ]);
      Route::put('/{id}', [
        MalfunctionListArticleController::class,
        'updateMalfunctionListArticle',
      ]);
      Route::delete('/{id}', [
        MalfunctionListArticleController::class,
        'deleteMalfunctionListArticle',
      ]);

      // Malfunction lists
      Route::group(['prefix' => '/{articleId}/malfunction-lists'], function () {
        Route::get('/', [
          MalfunctionListController::class,
          'getMalfunctionLists',
        ]);
        Route::post('/', [
          MalfunctionListController::class,
          'addMalfunctionList',
        ]);
        Route::post('/upload', [MalfunctionListController::class, 'upload']);
        Route::get('/{id}', [
          MalfunctionListController::class,
          'getMalfunctionList',
        ]);
        Route::put('/{id}', [
          MalfunctionListController::class,
          'updateMalfunctionList',
        ]);
        Route::delete('/{id}', [
          MalfunctionListController::class,
          'deleteMalfunctionList',
        ]);
      });
    });

    // Road safety law articles
    Route::group(['prefix' => '/road-safety-law-articles'], function () {
      Route::get('/', [
        RoadSafetyLawArticlesController::class,
        'getRoadSafetyLawArticles',
      ]);
      Route::post('/', [
        RoadSafetyLawArticlesController::class,
        'addRoadSafetyLawArticle',
      ]);
      Route::get('/{id}', [
        RoadSafetyLawArticlesController::class,
        'getRoadSafetyLawArticle',
      ]);
      Route::put('/{id}', [
        RoadSafetyLawArticlesController::class,
        'updateRoadSafetyLawArticle',
      ]);
      Route::delete('/{id}', [
        RoadSafetyLawArticlesController::class,
        'deleteRoadSafetyLawArticle',
      ]);

      // Road safety laws
      Route::group(['prefix' => '/{articleId}/road-safety-laws'], function () {
        Route::get('/', [RoadSafetyLawsController::class, 'getRoadSafetyLaws']);
        Route::post('/', [RoadSafetyLawsController::class, 'addRoadSafetyLaw']);
        Route::post('/upload', [RoadSafetyLawsController::class, 'upload']);
        Route::get('/{id}', [
          RoadSafetyLawsController::class,
          'getRoadSafetyLaw',
        ]);
        Route::put('/{id}', [
          RoadSafetyLawsController::class,
          'updateRoadSafetyLaw',
        ]);
        Route::delete('/{id}', [
          RoadSafetyLawsController::class,
          'deleteRoadSafetyLaw',
        ]);
      });
    });

    // Theoretical part lessons
    Route::group(['prefix' => '/theoretical-part-lessons'], function () {
      Route::get('/', [LessonsController::class, 'getLessons']);
      Route::put('/{id}', [LessonsController::class, 'updateLesson']);
      Route::post('/price', [LessonsController::class, 'setPrice']);

      Route::group(['prefix' => '/{lessonId}/trainings'], function () {
        Route::get('/', [TrainingsController::class, 'getTrainings']);
        Route::post('/', [TrainingsController::class, 'addTraining']);
        Route::get('/{id}', [TrainingsController::class, 'getTraining']);
        Route::put('/{id}', [TrainingsController::class, 'updateTraining']);
        Route::delete('/{id}', [TrainingsController::class, 'deleteTraining']);

        Route::group(['prefix' => '/{trainingId}/videos'], function () {
          Route::get('/', [VideosController::class, 'getVideos']);
          Route::post('/', [VideosController::class, 'addVideo']);
          Route::get('/{id}', [VideosController::class, 'getVideo']);
          Route::put('/{id}', [VideosController::class, 'updateVideo']);
          Route::delete('/{id}', [VideosController::class, 'deleteVideo']);
        });
      });
    });

    Route::group(['prefix' => '/calendar'], function () {
      Route::get('/', [CalendarController::class, 'getAppointments']);
    });

    Route::group(['prefix' => '/payments-list'], function () {
      Route::get('/', [PaymentsListController::class, 'getPayments']);
    });

    // Privacy policy
    Route::group(['prefix' => '/privacy-policies'], function () {
      Route::get('/', [PrivacyPolicyController::class, 'getPrivacyPolicies']);
      Route::post('/', [PrivacyPolicyController::class, 'addPrivacyPolicy']);
      Route::get('/{id}', [PrivacyPolicyController::class, 'getPrivacyPolicy']);
      Route::put('/{id}', [
        PrivacyPolicyController::class,
        'updatePrivacyPolicy',
      ]);
      Route::delete('/{id}', [
        PrivacyPolicyController::class,
        'deletePrivacyPolicy',
      ]);
    });

    // Get all students count
    // Route::get('/', [UsersController::class, 'getStudentsCount']);

    // Get all instructors count
    // Route::get('/', [UsersController::class, 'getInstructorsCount']);

    // Get current mount transactions
    Route::get('/transactions/total', [
      UsersController::class,
      'getTransactionsTotal',
    ]);
  }
);
