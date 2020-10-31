<?php

namespace Drupal\butils;

use Drupal\file\Entity\File;

/**
 * Trait ImageStyleTrait.
 *
 * Provides image styles related utils.
 */
trait ImageStyleTrait {

  /**
   * Flush all derivatives of an image style.
   *
   * @param string $id
   *   Style name.
   *
   * @return bool
   *   Operation result.
   */
  public function flushImageStyle($id) {
    $style = $this->entityTypeManager->getStorage('image_style')->load($id);
    if (!empty($style)) {
      $style->flush();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Flush all derivatives of all image styles.
   *
   * @return bool
   *   Operation result.
   */
  public function flushAllImageStyles() {
    $styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    foreach ($styles as $style) {
      $style->flush();
    }
    return TRUE;
  }

  /**
   * Flush all derivatives of a file's image style.
   *
   * @param \Drupal\butils\File $file
   *   File entity.
   * @param string $id
   *   Style name.
   *
   * @return bool
   *   Operation result.
   */
  public function flushFileImageStyle(File $file, $id) {
    $style = $this->entityTypeManager->getStorage('image_style')->load($id);
    if (!empty($style)) {
      $style->flush($file->getFileUri());
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Flush all derivatives a file's all image styles.
   *
   * @param \Drupal\butils\File $file
   *   File entity.
   *
   * @return bool
   *   Operation result.
   */
  public function flushFileAllImageStyles(File $file) {
    $styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    $uri = $file->getFileUri();
    foreach ($styles as $style) {
      $style->flush($uri);
    }
    return TRUE;
  }

}
