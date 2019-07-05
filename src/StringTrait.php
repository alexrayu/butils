<?php

namespace Drupal\butils;

/**
 * Trait String.
 *
 * Html related utils.
 */
trait StringTrait {

  /**
   * Clean up the UTF string.
   *
   * Removes the invalid characters.
   *
   * @param string $string
   *   Source html.
   *
   * @return string
   *   Cleaned-up string.
   */
  public function cleanString($string) {
    $string = preg_replace(array('@([\xef][\xbf][\xbf])@', '@[\x00-\x08\x0B\x0C\x0E-\x1F]@'), ' ', $string);
    $string = str_replace('&#10;', '', $string);
    $string = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $string);
    $string = trim($string);

    return $string;
  }

}
