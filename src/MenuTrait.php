<?php

namespace Drupal\butils;

use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Trait Menu.
 *
 * Provides menu related utils.
 */
trait MenuTrait {

  /**
   * Provides an array of menu links with paths and titles.
   *
   * @param string $menu_name
   *   Name of the menu.
   *
   * @return array
   *   Menu array.
   */
  public function menuTreeLinks($menu_name) {
    $menu_tmp = $this->menuBuild($menu_name);
    $menu = [];
    if (!empty($menu_tmp['#items'])) {
      foreach ($menu_tmp['#items'] as $item) {
        $childItems = [];
        if (!empty($item['below'])) {
          foreach ($item['below'] as $child) {
            $childItems[] = [
              'href' => $child['url']->toString(),
              'label' => $child['title'],
            ];
          }
        }
        $menu[] = [
          'href' => $item['url']->toString(),
          'label' => $item['title'],
          'children' => !empty($item['below']) ? $childItems : '',
        ];
      }
    }

    return $menu;
  }

  /**
   * Get menu tree of a menu by name.
   *
   * @param string $menu_name
   *   Existing menu name.
   *
   * @return array
   *   Array of menu link elements.
   */
  public function menuTree($menu_name) {
    $parameters = new MenuTreeParameters();
    $parameters->onlyEnabledLinks();
    $tree = $this->menuTree->load($menu_name, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];

    return $this->menuTree->transform($tree, $manipulators);
  }

  /**
   * Build render array of a menu by name.
   *
   * @param string $menu_name
   *   Existing menu name.
   *
   * @return array
   *   Built menu tree.
   */
  public function menuBuild($menu_name) {
    return $this->menuTree->build($this->menuTree($menu_name));
  }

}
