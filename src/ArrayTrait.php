<?php

namespace Drupal\butils;

/**
 * Trait Array.
 *
 * Taxonomy related utils.
 */
trait ArrayTrait {

  /**
   * Get a key from array by paths, trying one after another.
   *
   * @param string|array $paths
   *   Key identifiers, like "key.key2.key3". If array, it will try map one
   *   by one.
   * @param array $data
   *   Data array.
   *
   * @return bool|mixed
   *   Result if any.
   */
  protected function arrayMap($paths, array $data) {
    $paths = (array) $paths;
    foreach ($paths as $path) {
      $keys = explode('.', $path);
      $temp_data = $data;
      foreach ($keys as $key) {
        if (!empty($temp_data[$key])) {
          $temp_data = $temp_data[$key];
        }
        else {
          $temp_data = '';
          break;
        }
      }
      if (!empty($temp_data)) {
        return $temp_data;
      }
    }
  }

}
