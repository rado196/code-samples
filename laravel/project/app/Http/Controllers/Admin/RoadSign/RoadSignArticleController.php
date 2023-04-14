<?php

namespace App\Http\Controllers\Admin\RoadSign;

use App\Http\Controllers\Controller;
use App\Models\RoadSigns\RoadSignArticle;
use App\Models\RoadSigns\RoadSignArticleTranslation;
use Illuminate\Http\Request;

class RoadSignArticleController extends Controller
{
  private function createTranslations(Request $request, $roadSignArticleId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      RoadSignArticleTranslation::create([
        'article_id' => $roadSignArticleId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($roadSignArticleId)
  {
    RoadSignArticleTranslation::query()
      ->where('article_id', $roadSignArticleId)
      ->delete();
  }

  /**
   * GET: /api/admin/road-sign-articles
   */
  public function getRoadSignArticles()
  {
    $data = RoadSignArticle::query()->get();

    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * GET: /api/admin/road-sign-articles/{id}
   */
  public function getRoadSignArticle($id)
  {
    $roadSignArticle = RoadSignArticle::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'road_sign_article' => $roadSignArticle,
    ]);
  }

  /**
   * POST: /api/admin/road-sign-articles
   */
  public function addRoadSignArticle(Request $request)
  {
    $roadSignArticle = RoadSignArticle::create([]);

    $this->createTranslations($request, $roadSignArticle->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/road-sign-articles/{id}
   */
  public function updateRoadSignArticle(Request $request, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/road-sign-articles/{id}
   */
  public function deleteRoadSignArticle($id)
  {
    $this->cleanupTranslations($id);

    RoadSignArticle::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
