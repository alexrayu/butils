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

}
