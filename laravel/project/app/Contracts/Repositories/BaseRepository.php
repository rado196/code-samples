<?php

namespace App\Contracts\Repositories;

use JasonGuru\LaravelMakeRepository\Repository\BaseRepository as LaravelBaseRepository;

abstract class BaseRepository extends LaravelBaseRepository
{
  private static $instance = null;

  /**
   * Create singleton instance.
   */
  public static function factory()
  {
    if (is_null(self::$instance)) {
      self::$instance = new static();
    }

    return self::$instance;
  }

  /**
   * Make constructor in-accessible.
   */
  private function __construct()
  {
  }
}
