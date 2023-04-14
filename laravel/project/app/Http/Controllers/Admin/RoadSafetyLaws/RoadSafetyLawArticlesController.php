<?php

namespace App\Http\Controllers\Admin\RoadSafetyLaws;

use App\Http\Controllers\Controller;
use App\Models\RoadSafetyLaws\RoadSafetyLawArticles;
use App\Models\RoadSafetyLaws\RoadSafetyLawArticleTranslation;
use Illuminate\Http\Request;

class RoadSafetyLawArticlesController extends Controller
{
  private function createTranslations(Request $request, $roadSafetyLawArticleId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      RoadSafetyLawArticleTranslation::create([
        'article_id' => $roadSafetyLawArticleId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($roadSafetyLawArticleId)
  {
    RoadSafetyLawArticleTranslation::query()
      ->where('article_id', $roadSafetyLawArticleId)
      ->delete();
  }

  /**
   * GET: /api/admin/road-safety-law-articles
   */
  public function getRoadSafetyLawArticles()
  {
    $data = RoadSafetyLawArticles::query()->get();

    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * GET: /api/admin/road-safety-law-articles/{id}
   */
  public function getRoadSafetyLawArticle($id)
  {
    $roadSafetyLawArticle = RoadSafetyLawArticles::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'road_safety_law_article' => $roadSafetyLawArticle,
    ]);
  }

  /**
   * POST: /api/admin/road-safety-law-articles
   */
  public function addRoadSafetyLawArticle(Request $request)
  {
    $roadSafetyLawArticle = RoadSafetyLawArticles::create([]);

    $this->createTranslations($request, $roadSafetyLawArticle->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/road-safety-law-articles/{id}
   */
  public function updateRoadSafetyLawArticle(Request $request, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/road-safety-law-articles/{id}
   */
  public function deleteRoadSafetyLawArticle($id)
  {
    $this->cleanupTranslations($id);

    RoadSafetyLawArticles::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
