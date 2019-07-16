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
   * @param array $options
   *   Set of options:
   *   - trim: trim output. Defaults to TRUE.
   *   - force_array: force output to always be an array. Defaults to TRUE.
   *
   * @return array|string
   *   Response values.
   */
  public function mapXmlValues($path, array $data, array $options = []) {
    $trim = isset($options['trim']) ? $options['trim'] : TRUE;
    $force_array = isset($options['force_array']) ? $options['force_array'] : TRUE;

    $path_parts = explode('.', $path);
    $dest_key = array_pop($path_parts);
    $path = implode('.', $path_parts);
    $value = !empty($path) ? $this->arrayMap($path, $data) : $this->arrayMap($dest_key, $data);
    $results = [];

    // Direct value.
    if (empty($path) && !empty($dest_key)) {
      $value = is_string($value) && $trim ? trim($value) : $value;
      return $force_array ? [$value] : $value;
    }

    // If empty return.
    if (empty($value)) {
      return $force_array ? [] : '';
    }

    // If directly set as single, return.
    if (isset($value[$dest_key])) {
      $value[$dest_key] = is_string($value[$dest_key]) && $trim ? trim($value[$dest_key]) : $value[$dest_key];
      $is_collection = $this->isCollection($value[$dest_key]);
      if ($force_array && (!is_array($value[$dest_key]) || !$is_collection)) {
        return [$value[$dest_key]];
      }
      else {
        return $value[$dest_key];
      }
    }

    // If an array, iterate.
    if (is_array($value)) {
      foreach ($value as $entry) {
        if (isset($entry[$dest_key])) {
          $value[$dest_key] = is_string($entry[$dest_key]) && $trim ? trim($entry[$dest_key]) : $entry[$dest_key];
          $results[] = $entry[$dest_key];
        }
      }
    }

    return $results;
  }

  /**
   * Check if array is a collection rather than the end value.
   *
   * A collection has numeric keys only.
   *
   * @param array $data
   *   Checked data.
   *
   * @return bool
   *   Result.
   */
  protected function isCollection(array $data) {
    foreach ($data as $key => $value) {
      if (!is_numeric($key)) {
        return FALSE;
      }
    }

    return TRUE;
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
