<?php

namespace Drupal\butils;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Trait RedirectsTrait.
 *
 * Redirects related utils.
 */
trait RedirectsTrait {

  /**
   * Create the redirect.
   *
   * @param string $path
   *   The path.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Entity that has a canonic url.
   */
  public function redirectToEntity($path, ContentEntityInterface $entity) {
    $type = $entity->getEntityTypeId();
    $target = 'entity:' . $type . '/' . $entity->id();
    $langcode = $entity->language()->getId();
    if (!redirect_repository()->findMatchingRedirect($path, [], $langcode)) {
      $redirect = $this->entityTypeManager->getStorage('redirect')->create([
        'redirect_source' => $path,
        'redirect_redirect' => $target,
        'language' => $langcode,
        'status_code' => 301,
      ]);
      $redirect->save();
    }
  }

}
