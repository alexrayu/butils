<?php

namespace Drupal\butils;

/**
 * Trait String.
 *
 * String handling utils.
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
    $string = str_replace('&#10;', '', $string);
    $string = str_replace('&#8201;', ' ', $string);
    $string = preg_replace(['@([\xef][\xbf][\xbf])@', '@[\x00-\x08\x0B\x0C\x0E-\x1F]@'], ' ', $string);
    $string = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $string);
    $string = trim($string);

    return $string;
  }
  
    /**
   * Brutally check if substrings between heystacks and needle sintersect.
   *
   * Comparison is case-insensitive..
   * Don't use this fn unless either needle or haystack is an array.
   *
   * @param string|array $needle
   *   Needle(s)
   * @param string|array $haystack
   *   Heystack(s)
   *
   * @return bool
   *   Whether in string.
   */
  public function inStr($needle, $haystack) {
    if (empty($needle) || empty($haystack)) {
      return FALSE;
    }
    $needles = (array) $needle;
    $haystacks = (array) $haystack;
    foreach ($needles as $needle) {
      foreach ($haystacks as $haystack) {
        if (strpos((string) strtolower($haystack), (string) strtolower($needle)) !== FALSE) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}
