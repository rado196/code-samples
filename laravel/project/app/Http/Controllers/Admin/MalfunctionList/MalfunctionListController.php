<?php

namespace App\Http\Controllers\Admin\MalfunctionList;

use App\Http\Controllers\Controller;
use App\Models\MalfunctionList\MalfunctionList;
use App\Models\MalfunctionList\MalfunctionListArticle;
use App\Models\MalfunctionList\MalfunctionListTranslation;
use App\Traits\UploadingTrait;
use Illuminate\Http\Request;

class MalfunctionListController extends Controller
{
  use UploadingTrait;

  private function createTranslations(
    Request $request,
    $articleId,
    $malfunctionListId
  ) {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      MalfunctionListTranslation::create([
        'malfunction_list_id' => $malfunctionListId,
        'language_id' => $singleData->lang_id,
        'content' => $singleData->content,
      ]);
    }
  }

  private function cleanupTranslations($malfunctionListId)
  {
    MalfunctionListTranslation::query()
      ->where('malfunction_list_id', $malfunctionListId)
      ->delete();
  }

  /**
   * GET: /api/admin/malfunction-list-articles/{articleId}/malfunction-lists
   */
  public function getMalfunctionLists(Request $request, $articleId)
  {
    $article = MalfunctionListArticle::query()
      ->whereId($articleId)
      ->withOut(['translations'])
      ->first();

    $malfunctionLists = MalfunctionList::query()
      ->where('malfunction_list_article_id', $articleId)
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'article' => $article,
      'malfunction_lists' => $malfunctionLists,
    ]);
  }

  /**
   * GET: /api/admin/malfunction-list-articles/{articleId}/malfunction-lists/{id}
   */
  public function getMalfunctionList($articleId, $id)
  {
    $malfunctionList = MalfunctionList::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'malfunction_list' => $malfunctionList,
    ]);
  }

  /**
   * POST: /api/admin/malfunction-list-articles/{articleId}/malfunction-lists
   */
  public function addMalfunctionList(Request $request, $articleId)
  {
    $malfunctionList = MalfunctionList::create([
      'malfunction_list_article_id' => $articleId,
    ]);

    // $this->uploadImages($request, $trafficRule->id);
    $this->createTranslations($request, $articleId, $malfunctionList->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * POST: /api/admin/malfunction-list-articles/{articleId}/malfunction-lists/upload
   */
  public function upload(Request $request)
  {
    $imageName = $this->uploadFile(
      $request->file('image'),
      self::$UPLOAD_FOLDER_MALFUNCTION_LISTS
    );

    return response()->json(
      [
        'location' => $imageName,
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/malfunction-list-articles/{articleId}/malfunction-lists/{id}
   */
  public function updateMalfunctionList(Request $request, $articleId, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $articleId, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/malfunction-list-articles/{articleId}/malfunction-lists/{id}
   */
  public function deleteMalfunctionList($articleId, $id)
  {
    $this->cleanupTranslations($id);

    MalfunctionList::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
