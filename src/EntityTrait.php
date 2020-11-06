<?php

namespace Drupal\butils;

use Drupal\Core\Entity\EntityInterface;
use Drupal\file\Entity\File;

/**
 * Trait Entity.
 *
 * Provides general entity related utils.
 */
trait EntityTrait {

  /**
   * Either gets the entity by values or creates a new one.
   *
   * @param string $type
   *   Entity type.
   * @param array $values
   *   Entity values.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Resulting entity.
   */
  public function toEntity($type, array $values) {
    $query = $this->entityTypeManager->getStorage($type)->getQuery();
    foreach ($values as $key => $value) {
      $query->condition($key, $value);
    }
    $ids = $query->execute();
    if (!empty($ids)) {
      $id = reset($ids);
      $entity = $this->entityTypeManager->getStorage($type)->load($id);
    }
    else {
      $entity = $this->entityTypeManager->getStorage($type)->create($values);
      $entity->save();
    }

    return $entity;
  }

  /**
   * Dereference: get value by path.
   *
   * Path should be in the form of field_1.fild2.fid.
   *
   * @param string $path
   *   Mapped path.
   * @param object $entity
   *   Source data.
   *
   * @return mixed
   *   Field result.
   */
  public function deref($path, $entity) {
    $path_parts = explode('.', $path);
    $entity_type = $entity->getEntityTypeId();

    // Drill through the entity mapping values.
    $item = $entity;
    foreach ($path_parts as $part) {
      array_shift($path_parts);

      // Item is object.
      if ($item instanceof EntityInterface) {

        // Not a webform submission. Handle as a usual entity.
        if ($item->hasField($part)) {
          $field = $item->get($part);
          $def = $field->getFieldDefinition();
          $field_type = $def->getType();

          // Numeric? Sub-item.
          $key = is_numeric(reset($path_parts)) ? array_shift($path_parts) : 0;

          // Get value depending on type.
          switch ($field_type) {

            // Address.
            case 'address':
              $item = $field->getValue($key);
              if (!empty($item)) {
                $item = reset($item);
              }
              break;

            // File or Image.
            case 'image':
            case 'file':
              $item = $field->getValue($key);
              if (!empty($item)) {
                $item = File::load(reset($item)['target_id']);
                if (!empty($item)) {
                  $item = $this->deref(implode('.', $path_parts), $item);
                }
                else {
                  $item = '';
                }
                break;
              }
              break;

            // Entity reference.
            case 'entity_reference':
            case 'entity_reference_revisions':
              $ref_settings = $def->getSettings();
              $target_type = $ref_settings['target_type'];
              $item = $field->get($key);
              if (!empty($item)) {
                $item = $item->target_id;
              }
              if (!empty($target_type) && is_numeric($item)) {
                $item = $this->entityTypeManager->getStorage($target_type)->load($item);
                $item = $this->deref(implode('.', $path_parts), $item);
              }
              else {
                $item = '';
              }
              break;

            default:
              $values = $field->getValue();
              if (count($values) > 1) {
                $item = [];
                foreach ($values as $value) {
                  $item[] = reset($value);
                }
              }
              else {
                $item = $field->getString();
              }
          }
        }

        // Some more options. Log error if fail.
        else {

          // Allow mapping webform submission value.
          if ($entity_type == 'webform_submission') {
            $data = $item->getData();
            $item = $data[$part] ?? '';
          }

          // No matches, return empty result.
          else {
            return '';
          }
        }
      }

      // Item is array.
      elseif (is_array($item)) {
        if (!empty($item[$part])) {
          $item = $item[$part];
        }
        else {

          // Array key empty. Return NULL.
          return '';
        }
      }
    }

    // Result is an entity. Get it's title or name.
    if ($item instanceof EntityInterface) {
      if ($item->hasField('title')) {
        $item = $item->title->getString();
      }
      elseif ($item->hasField('name')) {
        $item = $item->name->getString();
      }
    }

    return $item;
  }

  /**
   * Get all available view modes as key => value.
   *
   * @param string $entity_type
   *   Type of entity which view modes to get.
   * @param string $bundle
   *   Entity bundle if any. Required to get display settings.
   * @param bool $labels_only
   *   Whether to return only the labels, or actual view modes as well.
   *
   * @return array
   *   View modes.
   */
  public function getViewModes($entity_type, $bundle = '', $labels_only = TRUE) {
    if ($labels_only) {
      return !empty($bundle)
        ? $this->entityDisplayRepository->getViewModeOptionsByBundle($entity_type, $bundle)
        : $this->entityDisplayRepository->getViewModeOptions($entity_type);
    }
    $view_modes = [];
    $all_modes = $this->entityDisplayRepository->getViewModes($entity_type);
    $bundle_modes = !empty($bundle)
      ? $this->entityDisplayRepository->getViewModeOptionsByBundle($entity_type, $bundle)
      : [];
    $all_modes['default'] = [
      'label' => 'Default',
      'emulated' => TRUE,
    ];
    foreach ($all_modes as $name => $mode) {
      if (!empty($bundle_modes)) {
        if (!empty($bundle_modes[$name]) || $name == 'default') {
          $view_modes[$name] = $labels_only ? $mode['label'] : $mode;
          $view_modes[$name]['display'] = $this->entityDisplayRepository->getViewDisplay($entity_type, $bundle, $name);
        }
      }
      else {
        $view_modes[$name] = $labels_only ? $mode['label'] : $mode;
      }
    }

    return $view_modes;
  }

  /**
   * Counts words in an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity to render and count words.
   * @param string $view_mode
   *   View mode to render in.
   *
   * @return int
   *   Number of words.
   */
  public function entityCountWords(EntityInterface $entity, $view_mode = 'default') {
    $build = $this->entityTypeManager
      ->getViewBuilder($entity->getEntityTypeId())
      ->view($entity, $view_mode);
    $html = $this->renderer->renderRoot($build);
    return $this->countWords($html);
  }

  /**
   * Builds a build array for an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity to render and count words.
   * @param string $view_mode
   *   View mode to render in.
   *
   * @return array
   *   Build array.
   */
  public function entityBuild(EntityInterface $entity, $view_mode = 'default') {
    return $this->entityTypeManager
      ->getViewBuilder($entity->getEntityTypeId())
      ->view($entity, $view_mode);
  }

  /**
   * Renders an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity to render and count words.
   * @param string $view_mode
   *   View mode to render in.
   *
   * @return string
   *   Html output.
   */
  public function entityRender(EntityInterface $entity, $view_mode = 'default') {
    $build = $this->entityTypeManager
      ->getViewBuilder($entity->getEntityTypeId())
      ->view($entity, $view_mode);
    return $this->renderer->renderRoot($build);
  }

}
