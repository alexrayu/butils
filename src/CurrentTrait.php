<?php

namespace Drupal\butils;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Trait Current.
 *
 * Get the current states.
 */
trait CurrentTrait {

  /**
   * Get the current page entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Current page entity.
   */
  public function currentEntity() {
    $page_entity = &drupal_static('butils_page_entity');
    if (!empty($page_entity)) {
      return $page_entity;
    }
    $types = array_keys($this->entityTypeManager->getDefinitions());
    $types = array_merge($types, [
      'node_preview',
    ]);
    $page_entity = NULL;
    $params = $this->routeMatch->getParameters()->all();
    foreach ($types as $type) {
      if (!empty($params[$type])) {
        if (is_string($params[$type])) {
          $page_entity = $this->entityTypeManager->getStorage($type)->load($params[$type]);
        }
        else {
          $page_entity = $params[$type];
        }

        if ($page_entity instanceof EntityInterface) {
          return $page_entity;
        }
      }
    }

    return NULL;
  }

  /**
   * Get for the current language.
   *
   * @return \Drupal\Core\Language\LanguageInterface
   *   Current language.
   */
  public function currentLanguage() {
    return $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT);
  }

  /**
   * Get the current user account (as proxy interface).
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   Current user account.
   */
  public function currentUser() {
    return $this->currentUser;
  }

  /**
   * Get the current route.
   *
   * @return \Drupal\Core\Routing\RouteMatchInterface
   *   Current route.
   */
  public function currentRoute() {
    return $this->routeMatch;
  }

  /**
   * Get the current request.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   Current request.
   */
  public function currentRequest() {
    return $this->requestStack->getCurrentRequest();
  }

  /**
   * Get the current path.
   *
   * @return \Drupal\Core\Path\PathMatcherInterface
   *   Current path.
   */
  public function currentPath() {
    return $this->pathMatcher;
  }

  /**
   * Get whether the current page is front page.
   *
   * @return bool
   *   Check result.
   */
  public function isFrontPage() {
    return $this->pathMatcher->isFrontPage();
  }

  /**
   * Check if the current page is Layout Builder page.
   *
   * @return bool
   *   Check result.
   */
  public function isBuilder() {
    return strpos($this->routeMatch->getRouteName(), 'layout_builder') === 0;
  }

}
