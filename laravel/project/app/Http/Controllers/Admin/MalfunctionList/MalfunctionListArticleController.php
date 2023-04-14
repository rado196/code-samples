<?php

namespace App\Http\Controllers\Admin\MalfunctionList;

use App\Http\Controllers\Controller;
use App\Models\MalfunctionList\MalfunctionListArticle;
use App\Models\MalfunctionList\MalfunctionListArticleTranslation;
use Illuminate\Http\Request;

class MalfunctionListArticleController extends Controller
{
  private function createTranslations(
    Request $request,
    $malfunctionListArticleId
  ) {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      MalfunctionListArticleTranslation::create([
        'article_id' => $malfunctionListArticleId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
      ]);
    }
  }

  private function cleanupTranslations($malfunctionListArticleId)
  {
    MalfunctionListArticleTranslation::query()
      ->where('article_id', $malfunctionListArticleId)
      ->delete();
  }

  /**
   * GET: /api/admin/malfunction-list-articles
   */
  public function getMalfunctionListArticles()
  {
    $data = MalfunctionListArticle::query()->get();

    return response()->json([
      'data' => $data,
    ]);
  }

  /**
   * GET: /api/admin/malfunction-list-articles/{id}
   */
  public function getMalfunctionListArticle($id)
  {
    $malfunctionListArticle = MalfunctionListArticle::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'malfunction_list_article' => $malfunctionListArticle,
    ]);
  }

  /**
   * POST: /api/admin/malfunction-list-articles
   */
  public function addMalfunctionListArticle(Request $request)
  {
    $malfunctionListArticle = MalfunctionListArticle::create([]);

    $this->createTranslations($request, $malfunctionListArticle->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/malfunction-list-articles/{id}
   */
  public function updateMalfunctionListArticle(Request $request, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/malfunction-list-articles/{id}
   */
  public function deleteMalfunctionListArticle($id)
  {
    $this->cleanupTranslations($id);

    MalfunctionListArticle::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
