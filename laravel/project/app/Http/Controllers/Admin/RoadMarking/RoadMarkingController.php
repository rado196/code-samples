<?php

namespace App\Http\Controllers\Admin\RoadMarking;

use App\Http\Controllers\Controller;
use App\Models\RoadMarkings\RoadMarking;
use App\Models\RoadMarkings\RoadMarkingArticle;
use App\Models\RoadMarkings\RoadMarkingTranslation;
use App\Traits\UploadingTrait;
use Illuminate\Http\Request;

class RoadMarkingController extends Controller
{
  use UploadingTrait;

  private function createTranslations(
    Request $request,
    $articleId,
    $roadMarkingId
  ) {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      RoadMarkingTranslation::create([
        'road_marking_id' => $roadMarkingId,
        'language_id' => $singleData->lang_id,
        'content' => $singleData->content,
      ]);
    }
  }

  private function cleanupTranslations($roadMarkingId)
  {
    RoadMarkingTranslation::query()
      ->where('road_marking_id', $roadMarkingId)
      ->delete();
  }

  /**
   * GET: /api/admin/road-marking-articles/{articleId}/road-markings
   */
  public function getRoadMarkings(Request $request, $articleId)
  {
    $article = RoadMarkingArticle::query()
      ->whereId($articleId)
      ->withOut(['translations'])
      ->first();

    $roadMarkings = RoadMarking::query()
      ->where('road_marking_article_id', $articleId)
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'article' => $article,
      'road_markings' => $roadMarkings,
    ]);
  }

  /**
   * GET: /api/admin/road-marking-articles/{articleId}/road-markings/{id}
   */
  public function getRoadMarking($articleId, $id)
  {
    $roadMarking = RoadMarking::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'road_marking' => $roadMarking,
    ]);
  }

  /**
   * POST: /api/admin/road-marking-articles/{articleId}/road-markings
   */
  public function addRoadMarking(Request $request, $articleId)
  {
    $roadMarking = RoadMarking::create([
      'road_marking_article_id' => $articleId,
    ]);

    // $this->uploadImages($request, $trafficRule->id);
    $this->createTranslations($request, $articleId, $roadMarking->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * POST: /api/admin/road-marking-articles/{articleId}/road-markings/upload
   */
  public function upload(Request $request)
  {
    $imageName = $this->uploadFile(
      $request->file('image'),
      self::$UPLOAD_FOLDER_ROAD_MARKINGS
    );

    return response()->json(
      [
        'location' => $imageName,
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/road-marking-articles/{articleId}/road-markings/{id}
   */
  public function updateRoadMarking(Request $request, $articleId, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $articleId, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/road-marking-articles/{articleId}/road-markings/{id}
   */
  public function deleteRoadMarking($articleId, $id)
  {
    $this->cleanupTranslations($id);

    RoadMarking::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
