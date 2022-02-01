<?php

namespace Drupal\butils;

use Drupal\Core\File\FileSystemInterface;

/**
 * Trait CSV.
 *
 * CSV related utils.
 */
trait CsvTrait {

  /**
   * Load content of a csv file.
   *
   * @param string $path
   *   The path to the file.
   * @param string $key_id
   *   The name of the unique identifier in the csv file.
   * @param string $delimiter
   *   The delimiter for the CSV items.
   *
   * @return array
   *   Loaded csv.
   */
  public function loadCsv($path, $key_id = '', $delimiter = ''): array {
    $data = [];
    $header = [];
    $new_key = FALSE;
    $pos_id = 0;

    // Autodetect delimiter.
    if (!$delimiter) {
      $delimiter = $this->detectCsvDelimiter($path);
    }

    if ($handle = fopen($path, 'rb')) {

      // Get or calculate the header.
      if (($fragment = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        $header = $fragment;
        if (!empty($key_id)) {
          if (!in_array($key_id, $header, FALSE)) {
            $header = array_keys($header);
            $new_key = TRUE;
            $key_id = 'csv_uuid';
            array_unshift($header, $key_id);
            rewind($handle);
          }
        }
        else {
          $header = $fragment;
          $new_key = TRUE;
          $key_id = 'csv_uuid';
          array_unshift($header, $key_id);
        }
      }

      while (($fragment = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
        if ($new_key) {
          array_unshift($fragment, $pos_id);
          $joined = array_combine($header, $fragment);
          $data[$pos_id] = $joined;
          $pos_id++;
        }
        else {
          $joined = array_combine($header, $fragment);
          $data[$joined[$key_id]] = $joined;
        }
      }
      fclose($handle);
    }

    return ['header' => $header, 'data' => $data];
  }

  /**
   * Identify the csv delimiter.
   *
   * @param string $path
   *   Path to CSV file..
   *
   * @return string
   *   Detected delimiter
   */
  public function detectCsvDelimiter($path) {
    $delimiters = [
      ';' => 0,
      ',' => 0,
      "\t" => 0,
      "|" => 0,
    ];
    $handle = fopen($path, 'rb');
    $header = fgets($handle);
    fclose($handle);
    foreach ($delimiters as $delimiter => &$count) {
      $count = count(str_getcsv($header, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
  }

  /**
   * Write the csv file.
   *
   * @param string $path
   *   Path where to save file.
   * @param array $data
   *   Data array.
   * @param array $header
   *   Header if needs to be added.
   *
   * @return bool
   *   Operation result.
   */
  public function saveCsv($path, array $data, array $header = []): bool {
    if (!empty($header)) {
      array_unshift($data, $header);
    }
    $pathinfo = pathinfo($path);
    $this->fileSystem->prepareDirectory($pathinfo['dirname'],
      FileSystemInterface::CREATE_DIRECTORY
      | FileSystemInterface::MODIFY_PERMISSIONS);
    if ($handle = fopen($path, 'wb')) {
      foreach ($data as $line) {
        fputcsv($handle, $line);
      }
      fclose($handle);

      return TRUE;
    }

    return FALSE;
  }

}
