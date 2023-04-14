<?php

namespace App\Http\Controllers\Admin\RoadSign;

use App\Http\Controllers\Controller;
use App\Models\RoadSigns\RoadSign;
use App\Models\RoadSigns\RoadSignArticle;
use App\Models\RoadSigns\RoadSignTranslation;
use App\Traits\UploadingTrait;
use Illuminate\Http\Request;

class RoadSignController extends Controller
{
  use UploadingTrait;

  private function createTranslations(Request $request, $articleId, $roadSignId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      RoadSignTranslation::create([
        'road_sign_id' => $roadSignId,
        'language_id' => $singleData->lang_id,
        'content' => $singleData->content,
      ]);
    }
  }

  private function cleanupTranslations($roadSignId)
  {
    RoadSignTranslation::query()
      ->where('road_sign_id', $roadSignId)
      ->delete();
  }

  /**
   * GET: /api/admin/road-sign-articles/{articleId}/road-signs
   */
  public function getRoadSigns(Request $request, $articleId)
  {
    $article = RoadSignArticle::query()
      ->whereId($articleId)
      ->withOut(['translations'])
      ->first();

    $roadSigns = RoadSign::query()
      ->where('road_sign_article_id', $articleId)
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'article' => $article,
      'road_signs' => $roadSigns,
    ]);
  }

  /**
   * GET: /api/admin/road-sign-articles/{articleId}/road-signs/{id}
   */
  public function getRoadSign($articleId, $id)
  {
    $roadSign = RoadSign::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'road_sign' => $roadSign,
    ]);
  }

  /**
   * POST: /api/admin/road-sign-articles/{articleId}/road-signs
   */
  public function addRoadSign(Request $request, $articleId)
  {
    $roadSign = RoadSign::create([
      'road_sign_article_id' => $articleId,
    ]);

    // $this->uploadImages($request, $trafficRule->id);
    $this->createTranslations($request, $articleId, $roadSign->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * POST: /api/admin/road-sign-articles/{articleId}/road-signs/upload
   */
  public function upload(Request $request)
  {
    $imageName = $this->uploadFile(
      $request->file('image'),
      self::$UPLOAD_FOLDER_ROAD_SIGNS
    );

    return response()->json(
      [
        'location' => $imageName,
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/road-sign-articles/{articleId}/road-signs/{id}
   */
  public function updateRoadSign(Request $request, $articleId, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $articleId, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/road-sign-articles/{articleId}/road-signs/{id}
   */
  public function deleteRoadSign($articleId, $id)
  {
    $this->cleanupTranslations($id);

    RoadSign::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
