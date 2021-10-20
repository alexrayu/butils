<?php

namespace Drupal\butils;

/**
 * Trait Utils.
 *
 * Misc utils functions.
 */
trait UtilsTrait {

  /**
   * Format bytes for output.
   *
   * @param int $bytes
   *   Bytes to format.
   * @param int $precision
   *   Formatting precision.
   *
   * @return string
   *   Formatted string.
   */
  public function formatBytes($bytes, $precision = 0) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
  }

}
