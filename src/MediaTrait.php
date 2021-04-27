<?php

namespace Drupal\butils;

/**
 * Trait Media.
 *
 * Provides media related utils.
 */
trait MediaTrait {

  /**
   * Find media ids by file id.
   *
   * @param string $field_name
   *   Field name where the file is referenced.
   * @param int $fid
   *   File id.
   *
   * @return array
   *   Found items if any.
   */
  public function mediaByFid($field_name, $fid) {
    return $this->entityTypeManager->getStorage('media')->getQuery()
      ->condition($field_name, $fid)
      ->execute();
  }

  /**
   * Get the media's main file object.
   *
   * @param \Drupal\media\MediaInterface|string $media
   *   Media object.
   *
   * @return \Drupal\file\FileInterface|null
   *   File if any.
   */
  public function mediaFile($media) {
    if (is_numeric($media) || is_string($media)) {
      $media = $this->entityTypeManager->getStorage('media')->load($media);
    }
    if (empty($media)) {
      return NULL;
    }
    return $this->entityTypeManager->getStorage('file')->load(
      $media->getSource()->getSourceFieldValue($media)
    );
  }

}
