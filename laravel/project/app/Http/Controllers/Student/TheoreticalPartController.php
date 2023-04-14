<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MalfunctionList\MalfunctionListArticle;
use App\Models\RoadMarkings\RoadMarkingArticle;
use App\Models\RoadSafetyLaws\RoadSafetyLawArticles;
use App\Models\RoadSigns\RoadSignArticle;
use App\Models\TrafficRule\TrafficRuleArticle;
use App\Models\VehicleSigns\VehicleSign;
use Illuminate\Http\Request;

class TheoreticalPartController extends Controller
{
  /**
   * GET: /api/student/theoretical-part/traffic-rules
   */
  public function getTrafficRules(Request $request)
  {
    $trafficRules = TrafficRuleArticle::query()
      ->without('translations')
      ->with('traffic_rule')
      ->get();

    return response()->json([
      'traffic_rules' => $trafficRules,
    ]);
  }

  /**
   * GET: /api/student/theoretical-part/road-signs
   */
  public function getRoadSigns(Request $request)
  {
    $roadSigns = RoadSignArticle::query()
      ->without('translations')
      ->with('road_sign')
      ->get();

    return response()->json([
      'road_signs' => $roadSigns,
    ]);
  }

  /**
   * GET: /api/student/theoretical-part/road-markings
   */
  public function getRoadMarkings(Request $request)
  {
    $roadMarkings = RoadMarkingArticle::query()
      ->without('translations')
      ->with('road_marking')
      ->get();

    return response()->json([
      'road_markings' => $roadMarkings,
    ]);
  }

  /**
   * GET: /api/student/theoretical-part/vehicle-sign
   */
  public function getVehicleSign(Request $request)
  {
    $vehicleSign = VehicleSign::query()
      ->without('translations')
      ->first();

    return response()->json([
      'vehicle_sign' => $vehicleSign,
    ]);
  }

  /**
   * GET: /api/student/theoretical-part/malfunction-list
   */
  public function getMalfunctionList(Request $request)
  {
    $malfunctionList = MalfunctionListArticle::query()
      ->without('translations')
      ->with('malfunction_list')
      ->get();

    return response()->json([
      'malfunction_list' => $malfunctionList,
    ]);
  }

  /**
   * GET: /api/student/theoretical-part/road-safety-laws
   */
  public function getRoadSafetyLaws(Request $request)
  {
    $roadSafetyLaws = RoadSafetyLawArticles::query()
      ->without('translations')
      ->with('road_safety_law')
      ->get();

    return response()->json([
      'road_safety_laws' => $roadSafetyLaws,
    ]);
  }
}
