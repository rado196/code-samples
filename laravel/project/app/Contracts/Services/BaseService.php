<?php

namespace App\Contracts\Services;

abstract class BaseService
{
  private static $instance = null;

  /**
   * Create singleton instance.
   */
  final public static function factory()
  {
    if (is_null(self::$instance)) {
      self::$instance = new static();
    }

    return self::$instance;
  }

  /**
   * Make constructor protected.
   */
  protected function __construct()
  {
  }
}
