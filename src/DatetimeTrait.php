<?php

namespace Drupal\butils;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Trait Datetime.
 *
 * Datetime related utils.
 */
trait DatetimeTrait {

  /**
   * Converts date to timestamp with the time zone in mind.
   *
   * @param string $string
   *   Date string.
   * @param string $timezone
   *   The timezone of the original string.
   *
   * @return int
   *   Timestamp.
   */
  public function strToStamp($string, $timezone = 'UTC') {
    $string = trim($string);
    if (empty($string)) {
      return 0;
    }

    try {
      if (strpos($string, 'Z') === strlen($string)) {
        $string = substr($string, 0, -1);
      }

      $date = new \DateTime($string, new \DateTimeZone($timezone));
      return $date->getTimestamp();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

  /**
   * Format the string date into datetime. Needed for datetime fields.
   *
   * The value is converted to UTC.
   *
   * @param string $string
   *   String value.
   * @param string $timezone
   *   The timezone of the original string.
   *
   * @return string
   *   Datetime value.
   */
  public function strToDate($string, $timezone = 'UTC') {
    $string = trim($string);
    if (empty($string)) {
      return NULL;
    }

    try {
      $date = new \DateTime($string, new \DateTimeZone($timezone));
      $date->setTimezone(new \DateTimeZone('UTC'));
      return $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

  /**
   * Format the Drupal datetime date into timestamp. Needed for datetime fields.
   *
   * The value is converted from UTC.
   *
   * @param string $string
   *   Datetime value.
   * @param string $timezone
   *   The timezone of the original string.
   * @param string $timezone_to
   *   The timezone to convert the value to.
   *
   * @return int
   *   Timestamp value.
   */
  public function dateToStamp($string, $timezone = 'UTC', $timezone_to = NULL) {
    $string = trim($string);
    if (empty($string)) {
      return 0;
    }

    if (!$timezone_to) {
      $timezone_to = drupal_get_user_timezone();
    }
    try {
      $date = new \DateTime($string, new \DateTimeZone($timezone));
      $date->setTimezone(new \DateTimeZone($timezone_to));

      return $date->getTimestamp();
    }
    catch (\Exception $e) {
      return 0;
    }
  }

  /**
   * Formats the datetime string with time zone in mind.
   *
   * The value is converted from UTC.
   *
   * @param string $string
   *   Datetime value.
   * @param string $format
   *   Date format.
   * @param string $timezone
   *   The timezone of the original string.
   * @param string $timezone_to
   *   The timezone to convert the value to.
   *
   * @return string
   *   Formatted value.
   */
  public function dateToFormat($string, $format, $timezone = 'UTC', $timezone_to = NULL) {
    $string = trim($string);
    if (empty($string)) {
      return NULL;
    }

    if (!$timezone_to) {
      $timezone_to = drupal_get_user_timezone();
    }

    try {
      $string = str_replace('T', ' ', $string);
      $date = new \DateTime($string, new \DateTimeZone($timezone));
      $date->setTimezone(new \DateTimeZone($timezone_to));
      $timestamp = $date->getTimestamp();

      return \Drupal::service('date.formatter')->format($timestamp, $format);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

}
