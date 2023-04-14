<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadingTrait
{
  private static $UPLOAD_FOLDER_VEHICLE_SIGNS = 'vehicle-signs';
  private static $UPLOAD_FOLDER_EXAM_TEST_QUESTIONS = 'exam-test-questions';
  private static $UPLOAD_FOLDER_MALFUNCTION_LISTS = 'malfunction-lists';
  private static $UPLOAD_FOLDER_ROAD_MARKINGS = 'road-markings';
  private static $UPLOAD_FOLDER_ROAD_SAFETY_LAWS = 'road-safety-laws';
  private static $UPLOAD_FOLDER_ROAD_SIGNS = 'road-signs';
  private static $UPLOAD_FOLDER_TRAINING_VIDEOS = 'training-videos';
  private static $UPLOAD_FOLDER_TRAFFIC_RULES = 'traffic-rules';

  private function buildFileName(UploadedFile $file)
  {
    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
    return strtolower($fileName);
  }

  private function buildStoragePath(
    string $folder,
    $groupFolder = null,
    $fileName = null
  ) {
    $list = ['uploads', $folder];
    if (!is_null($groupFolder)) {
      if (is_array($groupFolder)) {
        $groupFolder = implode(DIRECTORY_SEPARATOR, $groupFolder);
      }

      $list[] = $groupFolder;
    }
    if (!is_null($fileName)) {
      $list[] = $fileName;
    }

    return implode(DIRECTORY_SEPARATOR, $list);
  }

  private function uploadFile(
    UploadedFile $file,
    string $folder,
    $groupFolder = null
  ) {
    $fileName = $this->buildFileName($file);

    $filePath = $this->buildStoragePath($folder, $groupFolder);
    $filePath = storage_path('app/public/' . $filePath);

    if (!file_exists($filePath)) {
      mkdir($filePath, 0777, true);
    }

    $file->move($filePath, $fileName);

    return $fileName;
  }

  // private function uploadedPath()
}
