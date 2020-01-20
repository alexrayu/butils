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
    $query = \Drupal::entityQuery($type);
    foreach ($values as $key => $value) {
      $query->condition($key, $value);
    }
    $ids = $query->execute();
    if (!empty($ids)) {
      $id = reset($ids);
      $entity = $this->entityTypeManager->getStorage($type)->load($id);
    }
    else {
      $entity = $this->entityTypeManager->getStorage($type)->create($values)->save();
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
   *
   * @return array
   *   View modes.
   */
  public function getViewModes($entity_type) {
    $view_modes = [];
    $all_modes = $this->entityDisplayRepository->getViewModes($entity_type);
    foreach ($all_modes as $name => $mode) {
      $view_modes[$name] = $mode['label'];
    }

    return $view_modes;
  }

}
