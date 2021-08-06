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

  /**
   * Loads entity of type by id.
   *
   * @param string $type
   *   Entity type.
   * @param string $id
   *   Entity id.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   */
  public function entityLoad($type, $id) {
    return $this->entityTypeManager->getStorage($type)->load($id);
  }

  /**
   * Get entity query by type.
   *
   * @param string $type
   *   Entity type.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface|null
   */
  public function entityQuery($type) {
    return $this->entityTypeManager->getStorage($type)->getQuery();
  }

  /**
   * Check existance of entity type.
   *
   * @param string $type
   *   Entity type.
   *
   * @return bool
   *   Operation result.
   */
  public function entityTypeExists($type) {
    return $this->entityTypeManager->hasDefinition($type);
  }

  /**
   * Check existance of entity of type by id.
   *
   * @param string $type
   *   Entity type.
   * @param string $id
   *   Entity id.
   *
   * @return bool
   *   Operation result.
   */
  public function entityExists($type, $id) {
    if (!$this->entityTypeExists($type)) {
      return FALSE;
    }
    $query = $this->entityQuery($type);
    $key = $this->entityTypeManager->getDefinition($type)->getKey('id');
    return (!empty($query->condition($key, $id)->execute()));
  }

  /**
   * Map string value to entity field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Destination entity.
   * @param string $field_name
   *   Name of the field.
   * @param string $value
   *   Value to map.
   * @param string $input_format
   *   Input format if any. Used for textareas.
   *
   * @return bool
   *   Operation result.
   */
  public function mapString(EntityInterface $entity, $field_name, $value, $input_format = NULL) {
    if (!$entity->hasField($field_name)) {
      return FALSE;
    }
    if (!empty($value)) {
      if ($input_format) {
        $entity->set($field_name, [
          'value' => $value,
          'format' => $input_format,
        ]);
      }
      else {
        $entity->set($field_name, $value);
      }
    }
    else {
      $this->emptyField($entity, $field_name, FALSE);
    }

    return TRUE;
  }

  /**
   * Map some value to entity field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Destination entity.
   * @param string $field_name
   *   Name of the field.
   * @param string|array $value
   *   Value to map.
   *
   * @return bool
   *   Operation result.
   */
  public function mapValue(EntityInterface $entity, $field_name, $value) {
    if (!$entity->hasField($field_name)) {
      return FALSE;
    }
    if (!empty($value)) {
      $entity->set($field_name, $value);
    }
    else {
      $this->emptyField($entity, $field_name, FALSE);
    }

    return TRUE;
  }

  /**
   * Map boolean value to an entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Node object.
   * @param string $field_name
   *   Name of the field.
   * @param string $value
   *   Value to map.
   *
   * @return bool
   *   Operation result.
   */
  public function mapBool(EntityInterface $entity, $field_name, $value) {
    if (!$entity->hasField($field_name)) {
      return FALSE;
    }
    $value = (bool) $this->cleanString($value);
    if ($value) {
      $entity->set($field_name, $value);
    }
    else {
      $this->emptyField($entity, $field_name, FALSE);
    }

    return TRUE;
  }

  /**
   * Map term value to an entity field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Node object.
   * @param string $field_name
   *   Name of the field.
   * @param string $vid
   *   Vocabulary id.
   * @param string|array $value
   *   Value to map.
   *
   * @return bool
   *   Operation result.
   */
  public function mapTerm(EntityInterface $entity, $field_name, $vid, $value) {
    if (!$entity->hasField($field_name)) {
      return FALSE;
    }
    $value = (array) $value;
    $tids = [];

    if (!empty($value)) {
      foreach ($value as $sub_value) {
        if (is_array($sub_value)) {
          $tids[] = $this->toHierarchicalTerms($sub_value, $vid);
        }
        else {
          $tids[] = $this->toTerm($sub_value, $vid);
        }
      }
      if (!empty($tids)) {
        $field_value = [];
        foreach ($tids as $tid) {
          if (!empty($tid)) {
            $field_value[] = [
              'target_id' => $tid,
            ];
          }
        }
        if (!empty($field_value)) {
          $entity->set($field_name, $field_value);
        }
      }
    }
    else {
      $this->emptyField($entity, $field_name, FALSE);
    }

    return TRUE;
  }

  /**
   * Map number value to an entity field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Node object.
   * @param string $field_name
   *   Name of the field.
   * @param string $value
   *   Value to map.
   *
   * @return bool
   *   Operation result.
   */
  public function mapNum(EntityInterface $entity, $field_name, $value) {
    if (!$entity->hasField($field_name)) {
      return FALSE;
    }
    $value = (float) $this->cleanString($value);
    if ($value) {
      $entity->set($field_name, $value);
    }
    else {
      $this->emptyField($entity, $field_name, FALSE);
    }

    return TRUE;
  }

}
