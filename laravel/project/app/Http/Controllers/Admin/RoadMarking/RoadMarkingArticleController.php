<?php

namespace App\Http\Controllers\Admin\RoadMarking;

use App\Http\Controllers\Controller;
use App\Models\RoadMarkings\RoadMarkingArticle;
use App\Models\RoadMarkings\RoadMarkingArticleTranslation;
use Illuminate\Http\Request;

class RoadMarkingArticleController extends Controller
{
  private function createTranslations(Request $request, $roadMarkingArticleId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      RoadMarkingArticleTranslation::create([
        'article_id' => $roadMarkingArticleId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($roadMarkingArticleId)
  {
    RoadMarkingArticleTranslation::query()
      ->where('article_id', $roadMarkingArticleId)
      ->delete();
  }

  /**
   * GET: /api/admin/road-marking-articles
   */
  public function getRoadMarkingArticles()
  {
    $data = RoadMarkingArticle::query()->get();

    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * GET: /api/admin/road-marking-articles/{id}
   */
  public function getRoadMarkingArticle($id)
  {
    $roadMarkingArticle = RoadMarkingArticle::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'road_marking_article' => $roadMarkingArticle,
    ]);
  }

  /**
   * POST: /api/admin/road-marking-articles
   */
  public function addRoadMarkingArticle(Request $request)
  {
    $roadMarkingArticle = RoadMarkingArticle::create([]);

    $this->createTranslations($request, $roadMarkingArticle->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/road-marking-articles/{id}
   */
  public function updateRoadMarkingArticle(Request $request, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/road-marking-articles/{id}
   */
  public function deleteRoadMarkingArticle($id)
  {
    $this->cleanupTranslations($id);

    RoadMarkingArticle::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
