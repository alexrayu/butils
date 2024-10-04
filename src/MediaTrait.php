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
      ->accessCheck(FALSE)
      ->execute();
  }

  /**
   * Get the media's main file object.
   *
   * @param \Drupal\media\MediaInterface|string $media
   *   Media object.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
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

  /**
   * Get the media's metadata.
   *
   * @param \Drupal\media\MediaInterface|null $media
   *   Media object.
   */
  public function mediaMetadata($media) {
    if (!$media) {
      return [];
    }
    $source = $media->getSource();
    $keys = array_keys($source->getMetadataAttributes());
    $metadata = [];
    foreach ($keys as $key) {
      $metadata[$key] = $source->getMetadata($media, $key);;
    }
    return $metadata;
  }

  /**
   * Generates an URL to the end file of the media.
   *
   * @param \Drupal\media\MediaInterface|null $media
   *   Source media.
   * @param bool $absolute
   *   Whether the generated url needs to be absolute.
   *
   * @return string|null
   *   URL string.
   */
  public function mediaFileUrl($media, $absolute = FALSE) {
    if (!$media) {
      return NULL;
    }
    $url = NULL;
    $file = $this->mediaFile($media);
    if (!empty($file)) {
      $url = $absolute
        ? $this->fileAbsoluteUrl($file) : $this->fileRelativeUrl($file);
    }
    return $url;
  }

  /**
   * View the media image in an image style.
   *
   * @param \Drupal\media\MediaInterface|null $media
   *   Media entity.
   * @param string $image_style_name
   *   Image style name.
   *
   * @return string
   *   Url to the image styled image.
   */
  public function mediaFileUrlImageStyle($media, $image_style_name = 'thumbnail') {
    if (is_numeric($media)) {
      $media = $this->entityTypeManager->getStorage('media')->load($media);
    }
    $file = $this->mediaFile($media);
    return $this->fileUrlImageStyle($file, $image_style_name);
  }

}
