<?php

namespace Drupal\butils;

use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * Trait Xml.
 *
 * Taxonomy related utils.
 */
trait XmlTrait {

  /**
   * Loads the content of an xml file.
   *
   * @param string $path
   *   File path.
   *
   * @return array
   *   Decoded data.
   */
  public function loadXmlFile($path) {
    $data = [];
    if (!file_exists($path)) {
      return $data;
    }

    $encoder = new XmlEncoder();
    $request_xml = file_get_contents($path);
    $request_xml = $this->cleanString($request_xml);
    return $encoder->decode($request_xml, 'xml');
  }

  /**
   * Get the XML array values, given the single/plural syntax diffrence.
   *
   * Use when you need to just get a single value out of the results array.
   *
   * @param string $path
   *   Reference path.
   * @param array $data
   *   Imported data.
   *
   * @return array
   *   Response values.
   */
  public function mapXmlValues($path, array $data) {
    $path_parts = explode('.', $path);
    $dest_key = array_pop($path_parts);
    $path = implode('.', $path_parts);
    $value = $this->arrayMap($path, $data);
    $results = [];

    // If empty return.
    if (empty($value)) {
      return [];
    }

    // If directly set as single, return.
    if (isset($value[$dest_key])) {
      return [$value[$dest_key]];
    }

    // If an array, iterate.
    if (is_array($value)) {
      foreach ($value as $entry) {
        if (isset($entry[$dest_key])) {
          $results[] = $entry[$dest_key];
        }
      }
    }

    return $results;
  }

  /**
   * Clean Win-specific characters from xml.
   *
   * @param string $xml
   *   XML text.
   *
   * @return string
   *   Fixed XML.
   */
  public function cleanXml($xml) {
    $search = [
      '&#132;',
      '&#212;',
      '&#213;',
      '&#210;',
      '&#211;',
      '&#209;',
      '&#208;',
      '&#201;',
      '&#145;',
      '&#146;',
      '&#147;',
      '&#148;',
      '&#151;',
      '&#150;',
      '&#133;',
      '&#194;',
    ];
    $replace = [
      '&#8220;',
      '&#8216;',
      '&#8217;',
      '&#8220;',
      '&#8221;',
      '&#8211;',
      '&#8212;',
      '&#8230;',
      '&#8216;',
      '&#8217;',
      '&#8220;',
      '&#8221;',
      '&#8211;',
      '&#8212;',
      '&#8230;',
      '',
    ];

    return str_replace($search, $replace, $xml);
  }

}
