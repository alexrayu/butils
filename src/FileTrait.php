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
   * @param string $start
   *   File name to start with.
   *
   * @return array
   *   Found files, full paths.
   */
  public function findFilesRecurive(array $paths, array $extensions, $start = '') {
    $files = [];

    // Search files iteratively.
    foreach ($paths as $path) {
      if (!file_exists($path)) {
        continue;
      }
      $dir_iterator = new \RecursiveDirectoryIterator($path);
      $iterator = new \RecursiveIteratorIterator($dir_iterator);
      foreach ($extensions as $extension) {
        $regex = new \RegexIterator($iterator, '/^.+\.' . $extension . '$/i', \RecursiveRegexIterator::GET_MATCH);
        $start_reached = empty($start) ? TRUE : FALSE;
        foreach ($regex as $item) {
          $item = reset($item);
          $pathinfo = pathinfo($item);
          if (!$start_reached) {
            if (strtolower($pathinfo['basename']) == $start || strtolower($pathinfo['filename']) == $start) {
              $start_reached = TRUE;
            }
          }
          if ($start_reached) {
            $files[] = $item;
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

  /**
   * Get file real path.
   *
   * @param string $uri
   *   File Uri.
   *
   * @return false|string
   *   Realpath if found.
   */
  public function fileRealPath($uri) {
    return $this->fileSystem->realpath($uri);
  }

}
