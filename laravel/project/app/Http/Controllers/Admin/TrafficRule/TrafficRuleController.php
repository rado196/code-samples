<?php

namespace App\Http\Controllers\Admin\TrafficRule;

use App\Http\Controllers\Controller;
use App\Models\TrafficRule\TrafficRule;
use App\Models\TrafficRule\TrafficRuleArticle;
use App\Models\TrafficRule\TrafficRuleTranslation;
use App\Traits\UploadingTrait;
use Illuminate\Http\Request;

class TrafficRuleController extends Controller
{
  use UploadingTrait;

  private function uploadImages(Request $request, $trafficRuleId)
  {
    $imageNames = [];
    if ($request->hasFile('image')) {
      foreach ($request->file('image') as $file) {
        $imageNames[] = $this->uploadFile(
          $file,
          self::$UPLOAD_FOLDER_TRAFFIC_RULES
        );
      }
    }

    // if (!empty($imageNames)) {
    //   foreach ($imageNames as $name) {
    //     TrafficRuleImage::create([
    //       'traffic_rule_id' => $trafficRuleId,
    //       'image' => $name,
    //     ]);
    //   }
    // }
  }

  private function createTranslations(
    Request $request,
    $articleId,
    $trafficRuleId
  ) {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      TrafficRuleTranslation::create([
        'traffic_rule_id' => $trafficRuleId,
        'language_id' => $singleData->lang_id,
        'content' => $singleData->content,
      ]);
    }
  }

  private function cleanupTranslations($trafficRuleId)
  {
    TrafficRuleTranslation::query()
      ->where('traffic_rule_id', $trafficRuleId)
      ->delete();
  }

  /**
   * GET: /api/admin/traffic-rule-articles/{articleId}/traffic-rules
   */
  public function getTrafficRules(Request $request, $articleId)
  {
    $article = TrafficRuleArticle::query()
      ->whereId($articleId)
      ->withOut(['translations'])
      ->first();

    $trafficRules = TrafficRule::query()
      ->where('traffic_rule_article_id', $articleId)
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'article' => $article,
      'traffic_rules' => $trafficRules,
    ]);
  }

  /**
   * GET: /api/admin/traffic-rule-articles/{articleId}/traffic-rules/{id}
   */
  public function getTrafficRule($articleId, $id)
  {
    $trafficRule = TrafficRule::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'traffic_rule' => $trafficRule,
    ]);
  }

  /**
   * POST: /api/admin/traffic-rule-articles/{articleId}/traffic-rules
   */
  public function addTrafficRule(Request $request, $articleId)
  {
    $trafficRule = TrafficRule::create([
      'traffic_rule_article_id' => $articleId,
    ]);

    // $this->uploadImages($request, $trafficRule->id);
    $this->createTranslations($request, $articleId, $trafficRule->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * POST: /api/admin/traffic-rule-articles/{articleId}/traffic-rules/upload
   */
  public function upload(Request $request)
  {
    $imageName = $this->uploadFile(
      $request->file('image'),
      self::$UPLOAD_FOLDER_TRAFFIC_RULES
    );

    return response()->json(
      [
        'location' => $imageName,
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/traffic-rule-articles/{articleId}/traffic-rules/{id}
   */
  public function updateTrafficRule(Request $request, $articleId, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $articleId, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/traffic-rule-articles/{articleId}/traffic-rules/{id}
   */
  public function deleteTrafficRule($articleId, $id)
  {
    $this->cleanupTranslations($id);

    TrafficRule::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
