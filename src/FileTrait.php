<?php

namespace Drupal\butils;

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
   * Generates absolute URL for the file.
   *
   * @param \Drupal\file\FileInterface $file
   *   File object.
   *
   * @return string
   *   Relative url.
   */
  public function fileAbsoluteUrl($file) {
    if (!$file) {
      return NULL;
    }
    return $this->uriToAbsolute($file->getFileUri());
  }

  /**
   * Generates relative URL for the file.
   *
   * @param \Drupal\file\FileInterface|null $file
   *   File object.
   *
   * @return string
   *   Relative url.
   */
  public function fileRelativeUrl($file) {
    if (!$file) {
      return NULL;
    }
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
    return $this->fileUrlGenerator->generateString($uri);
  }

  /**
   * Convert uri to absolute url.
   *
   * @param string $uri
   *   File uri.
   *
   * @return string
   *   Absolute url.
   */
  public function uriToAbsolute($uri) {
    return $this->fileUrlGenerator->generateAbsoluteString($uri);
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

  /**
   * View the file image in an image style.
   *
   * @param \Drupal\file\FileInterface $file
   *   File entity.
   * @param string $image_style_name
   *   Image style name.
   *
   * @return string
   *   Url to the image styled image.
   */
  public function fileUrlImageStyle($file, $image_style_name = 'thumbnail') {
    if (is_numeric($file)) {
      $file = $this->entityTypeManager->getStorage('file')->load($file);
    }
    $image_style = $this->entityTypeManager->getStorage('image_style')->load($image_style_name);
    if (empty($file) || empty($image_style)) {
      return NULL;
    }
    return $image_style->buildUrl($file->getFileUri());
  }

}
