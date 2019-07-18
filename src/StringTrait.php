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
    $chr_map = [
      "\xc2\x80" => "\xe2\x82\xac",
      "\xc2\x82" => "\xe2\x80\x9a",
      "\xc2\x83" => "\xc6\x92",
      "\xc2\x84" => "\xe2\x80\x9e",
      "\xc2\x85" => "\xe2\x80\xa6",
      "\xc2\x86" => "\xe2\x80\xa0",
      "\xc2\x87" => "\xe2\x80\xa1",
      "\xc2\x88" => "\xcb\x86",
      "\xc2\x89" => "\xe2\x80\xb0",
      "\xc2\x8a" => "\xc5\xa0",
      "\xc2\x8b" => "\xe2\x80\xb9",
      "\xc2\x8c" => "\xc5\x92",
      "\xc2\x8e" => "\xc5\xbd",
      "\xc2\x91" => "\xe2\x80\x98",
      "\xc2\x92" => "\xe2\x80\x99",
      "\xc2\x93" => "\xe2\x80\x9c",
      "\xc2\x94" => "\xe2\x80\x9d",
      "\xc2\x95" => "\xe2\x80\xa2",
      "\xc2\x96" => "\xe2\x80\x93",
      "\xc2\x97" => "\xe2\x80\x94",
      "\xc2\x98" => "\xcb\x9c",
      "\xc2\x99" => "\xe2\x84\xa2",
      "\xc2\x9a" => "\xc5\xa1",
      "\xc2\x9b" => "\xe2\x80\xba",
      "\xc2\x9c" => "\xc5\x93",
      "\xc2\x9e" => "\xc5\xbe",
      "\xc2\x9f" => "\xc5\xb8",
    ];
    $chr = array_keys($chr_map);
    $rpl = array_values($chr_map);
    $string = preg_replace(array('@([\xef][\xbf][\xbf])@', '@[\x00-\x08\x0B\x0C\x0E-\x1F]@'), ' ', $string);
    $string = str_replace('&#10;', '', $string);
    $string = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $string);
    $string = str_replace($chr, $rpl, html_entity_decode($string, ENT_QUOTES, "UTF-8"));
    $string = trim($string);

    return $string;
  }

}
