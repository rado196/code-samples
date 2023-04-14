<?php

namespace App\Http\Controllers\Admin\TheoreticalPartLesson;

use App\Http\Controllers\Controller;
use App\Models\TheoreticalPartTraining\TheoreticalPartLesson;
use App\Models\TheoreticalPartTraining\TheoreticalPartTraining;
use App\Models\TheoreticalPartTraining\TheoreticalPartTrainingVideo;
use App\Models\TheoreticalPartTraining\TheoreticalPartTrainingVideoTranslation;
use App\Traits\UploadingTrait;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Video\X264 as CodexX264;
use Illuminate\Http\Request;
use Throwable;

class VideosController extends Controller
{
  use UploadingTrait;

  private function baseVideoGeneration(
    int $videoId,
    string $originalVideoName,
    ?string $videoName = null,
    ?callable $appendCredentials = null
  ) {
    $path = $this->buildStoragePath(
      self::$UPLOAD_FOLDER_TRAINING_VIDEOS,
      $videoId
    );
    $videName = is_null($videoName) ? $originalVideoName : $videoName;

    $path .= '/' . $videName;

    $generation = \FFMpeg::fromDisk('public')->open($path);

    if (is_callable($appendCredentials)) {
      $appendCredentials($generation);
    }

    return $generation;
  }

  private function generateResizedVideos(
    $videoId,
    $originalVideoName,
    $videoName,
    $size,
    $sizeType
  ) {
    $generateResizedName = $sizeType . '_' . $originalVideoName;

    $this->baseVideoGeneration(
      $videoId,
      $originalVideoName,
      $videoName,
      function ($generation) use ($size, $videoId, $generateResizedName) {
        $savePath = $this->buildStoragePath(
          self::$UPLOAD_FOLDER_TRAINING_VIDEOS,
          $videoId,
          $generateResizedName
        );

        $generation
          ->export()
          ->inFormat(new CodexX264())
          ->resize($size['width'], $size['height'])
          ->save($savePath);
      }
    );
  }

  private function generateVideoPoster($videoId, $originalVideoName, $videoName)
  {
    $this->baseVideoGeneration(
      $videoId,
      $originalVideoName,
      $videoName,
      function ($generation) use ($videoId, $videoName) {
        $videoName = str_replace(['sm_', '.mp4'], '', $videoName);

        $savePath = $this->buildStoragePath(
          self::$UPLOAD_FOLDER_TRAINING_VIDEOS,
          $videoId,
          $videoName . '.png'
        );

        $generation
          ->getFrameFromSeconds(1)
          ->export()
          ->save($savePath);
      }
    );
  }

  private function isClipVideo($lessonId, $trainingId)
  {
    $firstLesson = TheoreticalPartLesson::first();
    $firstTraining = TheoreticalPartTraining::query()
      ->where('lesson_id', $lessonId)
      ->first();

    if ($firstLesson->id == $lessonId && $firstTraining->id == $trainingId) {
      $isExistsVideo = TheoreticalPartTrainingVideo::query()
        ->where('training_id', $trainingId)
        ->exists();

      return !$isExistsVideo;
    }

    return false;
  }

  private function clipVideo($videoId, $videoName)
  {
    foreach (TheoreticalPartTrainingVideo::VIDEO_SIZES as $key => $size) {
      $generateResizedName = $key . '_' . $videoName;

      $this->baseVideoGeneration(
        $videoId,
        $videoName,
        $generateResizedName,
        function ($generation) use ($size, $videoId, $generateResizedName) {
          $savePath = $this->buildStoragePath(
            self::$UPLOAD_FOLDER_TRAINING_VIDEOS,
            $videoId . '/clipped',
            $generateResizedName
          );

          $generation
            ->export()
            ->inFormat(new CodexX264())
            ->addFilter('-ss', TimeCode::fromSeconds(0))
            ->addFilter('-to', TimeCode::fromSeconds(60))
            ->save($savePath);
        }
      );
    }
  }

  private function uploadFiles(Request $request, $videoId, $isClipVideo)
  {
    $videoName = null;
    if ($request->hasFile('video')) {
      $videoName = $this->uploadFile(
        $request->file('video'),
        self::$UPLOAD_FOLDER_TRAINING_VIDEOS,
        $videoId
      );

      foreach (TheoreticalPartTrainingVideo::VIDEO_SIZES as $key => $size) {
        $this->generateResizedVideos($videoId, $videoName, null, $size, $key);
      }

      if ($isClipVideo) {
        $this->clipVideo($videoId, $videoName);
      }

      $this->generateVideoPoster($videoId, $videoName, 'sm_' . $videoName);
    }

    return [$videoName, str_replace('.mp4', '.png', $videoName)];
  }

  private function cleanupFiles($id)
  {
    $path = $this->buildStoragePath(self::$UPLOAD_FOLDER_TRAINING_VIDEOS, $id);

    if (!\File::exists($path)) {
      try {
        \File::deleteDirectory($path);
      } catch (Throwable $e) {
        dd($e);
      }
    }
  }

  private function createTranslations(
    Request $request,
    $lessonId,
    $trainingId,
    $videoId
  ) {
    $info = json_decode($request->info);

    foreach ($info as $key => $singleData) {
      TheoreticalPartTrainingVideoTranslation::create([
        'video_id' => $videoId,
        'language_id' => $singleData->lang_id,
        'title' => $singleData->title,
        'description' => $singleData->description,
      ]);
    }
  }

  private function cleanupTranslations($videoId)
  {
    TheoreticalPartTrainingVideoTranslation::query()
      ->where('video_id', $videoId)
      ->delete();
  }

  private function isShowCreationButton()
  {
    $q = TheoreticalPartTrainingVideo::query()
      ->whereNull('poster')
      ->exists();

    return !$q;
  }

  /**
   * GET: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{trainingId}/videos
   */
  public function getVideos(Request $request, $lessonId, $trainingId)
  {
    $training = TheoreticalPartTraining::query()
      ->whereId($trainingId)
      ->withOut(['translations'])
      ->first();

    $videos = TheoreticalPartTrainingVideo::query()
      ->where('training_id', $trainingId)
      ->get();

    $showCreationButton = $this->isShowCreationButton();

    return response()->json([
      'training' => $training,
      'videos' => $videos,
      'show_creation_button' => $showCreationButton,
      'status' => 'success',
    ]);
  }

  /**
   * GET: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{trainingId}/videos/{id}
   */
  public function getVideo(Request $request, $lessonId, $trainingId, $id)
  {
    $video = TheoreticalPartTrainingVideo::query()
      ->whereId($id)
      ->first();

    return response()->json([
      'video' => $video,
    ]);
  }

  /**
   * POST: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{trainingId}/videos
   */
  public function addVideo(Request $request, $lessonId, $trainingId)
  {
    ignore_user_abort(true);

    $isClipVideo = $this->isClipVideo($lessonId, $trainingId);

    $video = TheoreticalPartTrainingVideo::create([
      'training_id' => $trainingId,
    ]);

    $this->createTranslations($request, $lessonId, $trainingId, $video->id);

    [$videoName, $posterName] = $this->uploadFiles(
      $request,
      $video->id,
      $isClipVideo
    );

    $video = TheoreticalPartTrainingVideo::find($video->id);
    $video->name = $videoName;
    $video->poster = $posterName;
    $video->save();

    return response()->json(
      [
        'status' => 'success',
      ],
      201
    );
  }

  /**
   * PUT: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{trainingId}/videos/{id}
   */
  public function updateVideo(Request $request, $lessonId, $trainingId, $id)
  {
    ignore_user_abort(true);

    $this->cleanupTranslations($id);
    $this->createTranslations($request, $lessonId, $trainingId, $id);

    return response()->json([
      'status' => 'success',
    ]);
  }

  /**
   * DELETE: /api/admin/theoretical-part-lessons/{lessonId}/trainings/{trainingId}/videos/{id}
   */
  public function deleteVideo(Request $request, $lessonId, $trainingId, $id)
  {
    $this->cleanupFiles($id);

    TheoreticalPartTrainingVideo::destroy($id);

    return response()->json([
      'status' => 'success',
    ]);
  }
}
