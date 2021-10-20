<?php

namespace Drupal\butils;

use Drupal\Core\Entity\EntityInterface;

/**
 * Trait ImageStyleTrait.
 *
 * Provides image styles related utils.
 */
trait ImageStyleTrait {

  /**
   * Get image style URL or URI.
   *
   * @param string $image_uri
   *   Image URI.
   * @param string $id
   *   Image style id.
   * @param string $type
   *   Response type, url or uri.
   *
   * @return string
   *   Image style URL or URI.
   */
  public function imageStyleUrl($image_uri, $id, $type = 'url') {
    $style = $this->entityTypeManager->getStorage('image_style')->load($id);
    if (!empty($style)) {
      if ($type === 'url') {
        return $style->buildUrl($image_uri);
      }
      else {
        return $style->buildUri($image_uri);
      }
    }

    return NULL;
  }

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
   * @param \Drupal\Core\Entity\EntityInterface $file
   *   File entity.
   * @param string $id
   *   Style name.
   *
   * @return bool
   *   Operation result.
   */
  public function flushFileImageStyle(EntityInterface $file, $id) {
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
   * @param \Drupal\Core\Entity\EntityInterface $file
   *   File entity.
   *
   * @return bool
   *   Operation result.
   */
  public function flushFileAllImageStyles(EntityInterface $file) {
    $styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    $uri = $file->getFileUri();
    foreach ($styles as $style) {
      $style->flush($uri);
    }
    return TRUE;
  }

  /**
   * Rebuild derivatives for a file's image style.
   *
   * NOTE: Making sure the file is a valid image file is on you!
   *
   * @param \Drupal\Core\Entity\EntityInterface $file
   *   File entity.
   * @param array $styles
   *   Image styles to rebuild. If empty, all will be rebuilt.
   *
   * @return bool
   *   Operation result.
   */
  public function rebuildImageStyles(EntityInterface $file, array $styles = []) {
    $image_uri = $file->getFileUri();
    $all_styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();
    if (empty($styles)) {
      $styles = array_keys($all_styles);
    }
    foreach ($styles as $name) {
      if (empty($all_styles[$name])) {
        continue;
      }
      $style = $all_styles[$name];
      $destination = $style->buildUri($image_uri);
      $style->createDerivative($image_uri, $destination);
    }

    return TRUE;
  }

}
