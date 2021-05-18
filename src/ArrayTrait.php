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
  
  /**
   * Apply python-line array slicing syntax for a numeric keys array.
   *
   * Supported options:
   * [:], [0:3], [0, 1, 2]
   *
   * @param array $data
   *   Source array.
   * @param string $format
   *   Formatting string, like [1:4].
   *
   * @return array
   *   Resulting array.
   */
  public function arraySlice(array $data, $format = '') {
    if (empty($format)) {
      return $data;
    }
    $data = array_values($data);
    $max = count($data) - 1;
    $format = str_replace(['[', ']'], '', $format);

    // Colon notation.
    if (strpos($format, ':') !== FALSE) {
      $parts = explode(':', $format);
      if (empty($parts[0])) {
        $parts[0] = array_key_first($data);
      }
      if (empty($parts[1])) {
        $parts[1] = array_key_last($data);
      }
      $start = intval($parts[0]);
      $end = intval($parts[1]);
      if ($start < 0) {
        $start = $max - $start;
      }
      if ($end < 0) {
        $end = $max - $end;
      }
      $length = $end - $start;
      if ($start < 0 || $end < 0 || $length <= 0) {
        return [];
      }

      return array_slice($data, $start, $length, TRUE);
    }

    // Simple notation.
    $res = [];
    $parts = explode(',', $format);
    foreach ($parts as $part) {
      $part = trim($part);
      if (strlen($part)) {
        $part = intval($part);
        if (isset($data[$part])) {
          $res[] = $data[$part];
        }
      }
    }

    return $res;
  }

}
