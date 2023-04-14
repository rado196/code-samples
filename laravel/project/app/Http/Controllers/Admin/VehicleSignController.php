<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleSigns\VehicleSign;
use App\Models\VehicleSigns\VehicleSignTranslation;
use App\Traits\UploadingTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleSignController extends Controller
{
  use UploadingTrait;

  private function createTranslations(Request $request, $vehicleSignId)
  {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      VehicleSignTranslation::create([
        'vehicle_sign_id' => $vehicleSignId,
        'language_id' => $singleData->lang_id,
        'content' => $singleData->content,
      ]);
    }
  }

  private function cleanupTranslations($vehicleSignId)
  {
    VehicleSignTranslation::query()
      ->where('vehicle_sign_id', $vehicleSignId)
      ->delete();
  }

  /**
   * GET: /api/admin/vehicle-signs
   */
  public function getVehicleSigns(Request $request)
  {
    $vehicleSigns = VehicleSign::query()
      ->with(['translations', 'translation'])
      ->get();

    return response()->json([
      'vehicle_signs' => $vehicleSigns,
    ]);
  }

  /**
   * GET: /api/admin/vehicle-signs/{id}
   */
  public function getVehicleSign($id)
  {
    $vehicleSign = VehicleSign::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'vehicle_sign' => $vehicleSign,
    ]);
  }

  /**
   * POST: /api/admin/vehicle-signs
   */
  public function addVehicleSign(Request $request)
  {
    $vehicleSign = VehicleSign::create([]);

    $this->createTranslations($request, $vehicleSign->id);

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * POST: /api/admin/vehicle-signs/upload
   */
  public function upload(Request $request)
  {
    $imageName = $this->uploadFile(
      $request->file('image'),
      self::$UPLOAD_FOLDER_VEHICLE_SIGNS
    );

    return response()->json(
      [
        'location' => $imageName,
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/vehicle-signs/{id}
   */
  public function updateVehicleSign(Request $request, $id)
  {
    $this->cleanupTranslations($id);
    $this->createTranslations($request, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/vehicle-signs/{id}
   */
  public function deleteVehicleSign($id)
  {
    $this->cleanupTranslations($id);

    VehicleSign::query()
      ->whereId($id)
      ->delete();

    return response()->json([
      'status' => 'success',
    ]);
  }
}
