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
    $chr_map = array(
      "\xC2\x82" => "'",
      "\xC2\x84" => '"',
      "\xC2\x8B" => "'",
      "\xC2\x91" => "'",
      "\xC2\x92" => "'",
      "\xC2\x93" => '"',
      "\xC2\x94" => '"',
      "\xC2\x9B" => "'",
      "\xC2\xAB" => '"',
      "\xC2\xBB" => '"',
      "\xE2\x80\x98" => "'",
      "\xE2\x80\x99" => "'",
      "\xE2\x80\x9A" => "'",
      "\xE2\x80\x9B" => "'",
      "\xE2\x80\x9C" => '"',
      "\xE2\x80\x9D" => '"',
      "\xE2\x80\x9E" => '"',
      "\xE2\x80\x9F" => '"',
      "\xE2\x80\xB9" => "'",
      "\xE2\x80\xBA" => "'",
    );
    $chr = array_keys($chr_map);
    $rpl = array_values($chr_map);
    $string = str_replace($chr, $rpl, html_entity_decode($string, ENT_QUOTES, "UTF-8"));
    $string = preg_replace(array('@([\xef][\xbf][\xbf])@', '@[\x00-\x08\x0B\x0C\x0E-\x1F]@'), ' ', $string);
    $string = str_replace('&#10;', '', $string);
    $string = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $string);
    $string = trim($string);

    return $string;
  }

}
