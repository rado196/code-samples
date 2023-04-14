<?php

namespace App\Http\Controllers\Admin\TrafficRule;

use App\Http\Controllers\Controller;
use App\Models\TrafficRule\TrafficRuleArticle;
use App\Models\TrafficRule\TrafficRuleArticleTranslation;
use Illuminate\Http\Request;

class TrafficRuleArticleController extends Controller
{
  private function createTranslations(Request $request, $trafficRuleArticleId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      TrafficRuleArticleTranslation::create([
        'article_id' => $trafficRuleArticleId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($trafficRuleArticleId)
  {
    TrafficRuleArticleTranslation::query()
      ->where('article_id', $trafficRuleArticleId)
      ->delete();
  }

  /**
   * GET: /api/admin/traffic-rule-articles
   */
  public function getTrafficRuleArticles()
  {
    $data = TrafficRuleArticle::query()->get();

    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * GET: /api/admin/traffic-rule-articles/{id}
   */
  public function getTrafficRuleArticle($id)
  {
    $trafficRuleArticle = TrafficRuleArticle::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'traffic_rule_article' => $trafficRuleArticle,
    ]);
  }

  /**
   * POST: /api/admin/traffic-rule-articles
   */
  public function addTrafficRuleArticle(Request $request)
  {
    $trafficRuleArticle = TrafficRuleArticle::create([]);

    $this->createTranslations($request, $trafficRuleArticle->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/traffic-rule-articles/{id}
   */
  public function updateTrafficRuleArticle(Request $request, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/traffic-rule-articles/{id}
   */
  public function deleteTrafficRuleArticle($id)
  {
    $this->cleanupTranslations($id);

    TrafficRuleArticle::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
