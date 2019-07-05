<?php

namespace Drupal\butils;

use Drupal\file\Entity\File;

/**
 * Trait FileTrait.
 *
 * File related utils.
 */
trait FileTrait {

  /**
   * Finds files of specified extensions recursively.
   *
   * @param array $paths
   *   A set of root folders.
   * @param array $extensions
   *   Supported extensions without the starting dots.
   * @param int $start
   *   Start count (skip $start items).
   *
   * @return array
   *   Found files, full paths.
   */
  public function findFilesRecurive(array $paths, array $extensions, $start = 0) {
    $files = [];

    // Search files iteratively.
    $i = 0;
    foreach ($paths as $path) {
      $dir_iterator = new \RecursiveDirectoryIterator($path);
      $iterator = new \RecursiveIteratorIterator($dir_iterator);
      foreach ($extensions as $extension) {
        $regex = new \RegexIterator($iterator, '/^.+\.' . $extension . '$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $item) {
          $i++;
          if ($i >= $start) {
            $files[] = reset($item);
          }
        }
      }
    }

    return $files;
  }

  /**
   * Get relative URI for the file.
   *
   * @param \Drupal\file\Entity\File $file
   *   File object.
   *
   * @return string
   *   Relative url.
   */
  public function fileRelativeUrl(File $file) {
    return $this->uriToRelative($file->getFileUri());
  }

  /**
   * Convert uri to relative url.
   *
   * @param string $uri
   *   File uri.
   *
   * @return string
   *   Relative url.
   */
  public function uriToRelative($uri) {
    return file_url_transform_relative(file_create_url($uri));
  }

}
