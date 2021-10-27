<?php

namespace Drupal\butils\TwigExtension;

use Drupal\Core\Entity\EntityInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * SV Twig extensions.
 */
class ButilsTwigExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    return [
      new TwigFunction('entityBuild', [self::class, 'entityBuild']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('empty', [self::class, 'checkEmpty']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'butils.twig_extension';
  }

  /**
   * Custom check to tell if variable is empty. Can check 2-level arrays.
   *
   * @param mixed $value
   *   Value to check.
   *
   * @return bool
   *   Check result.
   */
  public static function checkEmpty($value) {
    if (is_array($value)) {
      $value = array_filter($value);
    }
    return !empty($value);
  }

  /**
   * Returns entity's build array. Wraps Butils::entityBuild.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity to build.
   * @param string $view_mode
   *   Entity view mode.
   *
   * @return array
   *   Entity build array..
   */
  public static function entityBuild(EntityInterface $entity, $view_mode = 'default') {
    return \Drupal::service('butils')->entityBuild($entity, $view_mode);
  }

}
