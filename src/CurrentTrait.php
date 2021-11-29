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
   * Node and user will be preferred if multiple entities are encountered.
   *
   * @param string $type
   *   Expected entity type. NULL will result if not met.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Current page entity.
   */
  public function currentEntity($type = '') {
    $page_entity = &drupal_static('butils_page_entity', NULL);
    if (!empty($page_entity)) {
      return $page_entity;
    }
    $current_entities = $this->currentEntities();
    if (!empty($current_entities)) {
      if (!empty($type)) {
        $page_entity = $current_entities[$type] ?? NULL;
      }
      else {
        $page_entity = reset($current_entities);
      }
    }

    return $page_entity;
  }

  /**
   * Get all current page entities.
   *
   * @return array
   *   Current page entities.
   */
  public function currentEntities() {
    $page_entities = &drupal_static('butils_page_entities');
    if (!empty($page_entities)) {
      return $page_entities;
    }
    $types = array_keys($this->entityTypeManager->getDefinitions());
    $types = array_merge([
      'node',
      'user',
      'node_preview',
    ], $types);
    $page_entities = [];
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
          $page_entities[$type] = $page_entity;
        }
      }
    }

    return $page_entities;
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
   * @return string
   *   Current request path like /content/mytitle.
   */
  public function currentPath() {
    return $this->requestStack->getCurrentRequest()->getRequestUri();
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

  /**
   * Imitation of Drupal 7's aarg() function.
   *
   * @return array
   *   Current path args.
   */
  public function arg() {
    return explode('/', trim($this->currentPath(), '/'));
  }

}
