<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy\PrivacyPolicy;
use App\Models\PrivacyPolicy\PrivacyPolicyTranslation;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
  private function createTranslations(Request $request, $privacyPolicyId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      PrivacyPolicyTranslation::create([
        'privacy_policy_id' => $privacyPolicyId,
        'language_id' => $singleData->lang_id,
        'content' => $singleData->content,
      ]);
    }
  }

  private function cleanupTranslations($privacyPolicyId)
  {
    PrivacyPolicyTranslation::query()
      ->where('privacy_policy_id', $privacyPolicyId)
      ->delete();
  }

  /**
   * GET: /api/admin/privacy-policies
   */
  public function getPrivacyPolicies(Request $request)
  {
    $privacyPolicies = PrivacyPolicy::query()
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'privacy_policies' => $privacyPolicies,
    ]);
  }

  /**
   * GET: /api/admin/privacy-policies/{id}
   */
  public function getPrivacyPolicy($id)
  {
    $privacyPolicy = PrivacyPolicy::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'privacy_policy' => $privacyPolicy,
    ]);
  }

  /**
   * POST: /api/admin/privacy-policies
   */
  public function addPrivacyPolicy(Request $request)
  {
    $privacyPolicy = PrivacyPolicy::create([]);

    // $this->uploadImages($request, $trafficRule->id);
    $this->createTranslations($request, $privacyPolicy->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/privacy-policies/{id}
   */
  public function updatePrivacyPolicy(Request $request, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/privacy-policies/{id}
   */
  public function deletePrivacyPolicy($id)
  {
    $this->cleanupTranslations($id);

    PrivacyPolicy::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
