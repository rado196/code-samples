<?php

namespace App\Http\Controllers\Admin\RoadSafetyLaws;

use App\Http\Controllers\Controller;
use App\Models\RoadSafetyLaws\RoadSafetyLaw;
use App\Models\RoadSafetyLaws\RoadSafetyLawArticles;
use App\Models\RoadSafetyLaws\RoadSafetyLawTranslation;
use App\Traits\UploadingTrait;
use Illuminate\Http\Request;

class RoadSafetyLawsController extends Controller
{
  use UploadingTrait;

  private function createTranslations(
    Request $request,
    $articleId,
    $roadSafetyLawId
  ) {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      RoadSafetyLawTranslation::create([
        'road_safety_law_id' => $roadSafetyLawId,
        'language_id' => $singleData->lang_id,
        'content' => $singleData->content,
      ]);
    }
  }

  private function cleanupTranslations($roadSafetyLawId)
  {
    RoadSafetyLawTranslation::query()
      ->where('road_safety_law_id', $roadSafetyLawId)
      ->delete();
  }

  /**
   * GET: /api/admin/road-safety-law-articles/{articleId}/road-safety-laws
   */
  public function getRoadSafetyLaws(Request $request, $articleId)
  {
    $article = RoadSafetyLawArticles::query()
      ->whereId($articleId)
      ->withOut(['translations'])
      ->first();

    $roadSafetyLaw = RoadSafetyLaw::query()
      ->where('road_safety_law_article_id', $articleId)
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'article' => $article,
      'road_safety_laws' => $roadSafetyLaw,
    ]);
  }

  /**
   * GET: /api/admin/road-safety-law-articles/{articleId}/road-safety-laws/{id}
   */
  public function getRoadSafetyLaw($articleId, $id)
  {
    $roadSafetyLaw = RoadSafetyLaw::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'road_safety_law' => $roadSafetyLaw,
    ]);
  }

  /**
   * POST: /api/admin/road-safety-law-articles/{articleId}/road-safety-laws
   */
  public function addRoadSafetyLaw(Request $request, $articleId)
  {
    $roadSafetyLaw = RoadSafetyLaw::create([
      'road_safety_law_article_id' => $articleId,
    ]);

    // $this->uploadImages($request, $trafficRule->id);
    $this->createTranslations($request, $articleId, $roadSafetyLaw->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * POST: /api/admin/road-safety-law-articles/{articleId}/road-safety-laws/upload
   */
  public function upload(Request $request)
  {
    $imageName = $this->uploadFile(
      $request->file('image'),
      self::$UPLOAD_FOLDER_ROAD_SAFETY_LAWS
    );

    return response()->json(
      [
        'location' => $imageName,
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/road-safety-law-articles/{articleId}/road-safety-laws/{id}
   */
  public function updateRoadSafetyLaw(Request $request, $articleId, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $articleId, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/road-safety-law-articles/{articleId}/road-safety-laws/{id}
   */
  public function deleteRoadSafetyLaw($articleId, $id)
  {
    $this->cleanupTranslations($id);

    RoadSafetyLaw::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
