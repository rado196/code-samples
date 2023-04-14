<?php

namespace App\Contracts\Models;

use Illuminate\Support\Str;

abstract class BaseModelObserver
{
  private static function fillObserverList($root, $dir, &$results = [])
  {
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $key => $value) {
      $path = realpath($dir . '/' . $value);
      if (!is_dir($path)) {
        if (Str::endsWith($path, 'Observer.php')) {
          $results[] = str_replace(
            [$root . '/', '/', '.php'],
            ['', '\\', ''],
            $path
          );
        }
      } else {
        self::fillObserverList($root, $path, $results);
      }
    }

    return $results;
  }

  /**
   * Register all observers.
   */
  public static function registerAllObservers()
  {
    $observersPath = realpath(__DIR__ . '/../../Observers');

    $observerClasses = [];
    self::fillObserverList($observersPath, $observersPath, $observerClasses);

    foreach ($observerClasses as $observerClassName) {
      $observerClass = '\\App\\Observers\\' . $observerClassName;
      $modelClass = $observerClass::model();

      $modelClass::observe($observerClass);
    }
  }

  /**
   * Get model class.
   */
  abstract protected static function model();

  // abstract function retrieved($model);
  // abstract function creating($model);
  // abstract function created($model);
  // abstract function updating($model);
  // abstract function updated($model);
  // abstract function saving($model);
  // abstract function saved($model);
  // abstract function deleting($model);
  // abstract function deleted($model);
  // abstract function restoring($model);
  // abstract function restored($model);
  // abstract function replicating($model);
}
