<?php

namespace Database\Seeders\DatabaseSeeders\Common;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class LinkStorageSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $publicPath = public_path();
    foreach (config('filesystems.links') as $pathInPublic => $pathInStorage) {
      $baseDir = dirname($pathInPublic);
      if ($publicPath == $baseDir) {
        continue;
      }

      if (!File::isDirectory($baseDir)) {
        File::makeDirectory($baseDir, 0755, true);
      }
    }

    Artisan::call('storage:link');
  }
}
