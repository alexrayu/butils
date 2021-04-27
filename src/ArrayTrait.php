<?php

namespace Drupal\butils;

/**
 * Trait Array.
 *
 * Array handling helper functions.
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

    return FALSE;
  }

  /**
   * Generates a diff between two arrays.
   *
   * @param array $array1
   *   First array.
   * @param array $array2
   *   Second array.
   *
   * @return array
   *   The diff.
   */
  public function arrayDiff(array $array1, array $array2) {
    $result = [];

    foreach ($array1 as $key => $val) {
      if (is_array($val) && isset($array2[$key])) {
        $tmp = $this->arrayDiff($val, $array2[$key]);
        if ($tmp) {
          $result[$key] = $tmp;
        }
      }
      elseif (!isset($array2[$key])) {
        $result[$key] = NULL;
      }
      elseif ($val !== $array2[$key]) {
        $result[$key] = $array2[$key];
      }
      if (isset($array2[$key])) {
        unset($array2[$key]);
      }
    }
    $result = array_merge($result, $array2);

    return $result;
  }

}
