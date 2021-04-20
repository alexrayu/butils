<?php

namespace Drupal\butils;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Field trait.
 *
 * Field related utils.
 */
trait FieldTrait {

  /**
   * Get all field extended definitions for an entity of a type.
   *
   * @param string $entity_type
   *   Entity type.
   * @param string $bundle
   *   Entity bundle.
   *
   * @return array
   *   List of field definitions.
   */
  public function getFieldDefinitions($entity_type, $bundle) {
    $list = $this->entityFieldManager->getFieldDefinitions($entity_type, $bundle);
    $extended_definitions = [];
    foreach ($list as $name => $definition) {
      $extended_definitions[$name] = [
        'field_name' => $name,
        'entity_type' => $entity_type,
        'bundle' => $bundle,
        'definition' => $definition,
      ];
      $extended_definitions[$name]['details'] = $this->getFieldDefinitionsDetails($extended_definitions[$name]);
    }

    return $extended_definitions;
  }

  /**
   * Gets the details of a filter.
   *
   * @param array $extended_definition
   *   Field's extended definition object.
   *
   * @return array
   *   Filter details usable for filtering.
   */
  protected function getFieldDefinitionsDetails(array $extended_definition) {
    $definition = $extended_definition['definition'];
    $form_display = $this->entityTypeManager->getStorage('entity_form_display')->load($extended_definition['entity_type'] . '.' . $extended_definition['bundle'] . '.default');
    $component = $form_display->getComponent($extended_definition['field_name']);
    $field_type = $definition->getType();

    // Get widget type.
    if (!empty($component['type'])) {
      $widget_type = $component['type'];
    }

    // Base fields have no component data. Guess from field type.
    else {
      $widget_type = 'boolean_checkbox';
      if ($field_type === 'entity_reference') {
        $widget_type = 'entity_reference_autocomplete';
      }
    }

    // Add new details to existing, flatten the render objects.
    $details = [
      'label' => $definition->getLabel(),
      'description' => $definition->getDescription(),
      'field_type' => $field_type,
      'widget' => $widget_type,
    ];
    $details += $extended_definition;
    if (is_object($details['description'])) {
      $details['description'] = render($details['description']);
    }
    if (is_object($details['label'])) {
      $details['description'] = render($details['label']);
    }

    // Additions for entity reference.
    if ($field_type === 'entity_reference') {
      $details['ref_target_type'] = $definition->getSetting('target_type');
      $handler_settings = $definition->getSetting('handler_settings');
      if (!empty($handler_settings['target_bundles'])) {
        $details['ref_target_bundle'] = reset($handler_settings['target_bundles']);
      }
      else {
        $details['ref_target_bundle'] = '';
      }
    }

    return $details;
  }

  /**
   * Empty an entity's field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity posessing the field.
   * @param string $field_name
   *   Field name.
   * @param bool $save
   *   Whether or not the entity should be saved at the end.
   */
  public function emptyField(EntityInterface $entity, $field_name, $save = TRUE) {
    if (!$entity instanceof FieldableEntityInterface || !$entity->hasField($field_name)) {
      return;
    }
    $items = $entity->get($field_name);
    $has_items = !!$items->count();
    for ($i = 0; $i < $items->count(); $i++) {
      $items->removeItem($i);

      // Adjust for a rekey().
      $i--;
    }
    if ($has_items && $save) {
      $entity->save();
    }
  }

  /**
   * View field value without the wrappers and label.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity posessing the field.
   * @param string $field_name
   *   Field name.
   * @param array $options
   *   Render options.
   *     - display: Render display mode. Default "default".
   *     - multiple: Whether to render multivalue items. Default TRUE.
   *
   * @return array
   *   Build array.
   */
  public function viewField(EntityInterface $entity, $field_name, array $options = []) {
    $options['display'] = $options['display'] ?? 'default';
    $options['multiple'] = $options['multiple'] ?? TRUE;
    if (!$entity instanceof FieldableEntityInterface
      || !$entity->hasField($field_name)) {
      return [];
    }

    // Build.
    $field_item = $entity->{$field_name};
    $field_build = [];
    if (!empty($field_item)) {
      $definition = $field_item->getFieldDefinition();
      $field_build = $field_item->view($options['display']);
      $field_build['#field_type'] = $definition->get('field_type');
      $field_build['#label_display'] = 'hidden';
    }

    // Reduce.
    if (empty($options['multiple'])) {
      $count = $field_item->count();
      if ($count > 1) {
        for ($x = 1; $x <= $count; $x++) {
          unset($field_build[$x]);
        }
      }
    }

    return $field_build;
  }

  /**
   * Renders field value without the wrappers and label.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity posessing the field.
   * @param string $field_name
   *   Field name.
   * @param array $options
   *   Render options.
   *     - display: Render display mode. Default "default".
   *     - multiple: Whether to render multivalue items. Default TRUE.
   *     - full_html: Whether to render full html. Default FALSE.
   *
   * @return string
   *   Field value.
   */
  public function renderField(EntityInterface $entity, $field_name, array $options = []) {
    $field_build = $this->viewField($entity, $field_name, $options);

    switch ($field_build['#field_type']) {
      case 'file':
      case 'image':
        $result = (string) $this->renderer->renderRoot($field_build);
        break;

      default:
        $result = (string) $this->renderer->renderRoot($field_build);
        if (empty($options['full_html'])) {
          $result = strip_tags($result);
        }
    }
    $result = $this->cleanHtml($result);

    return $result;
  }

  /**
   * A hack to get field value without a Node::load().
   *
   * To be used for speed critical cases, use Node::load() otherwise.
   *
   * @param int|array $entity_id
   *   Entity id/ids.
   * @param string $field_name
   *   Name of the field.
   * @param string $entity_type
   *   Entity type machine string, like "node".
   * @param int $delta
   *   Field value Delta.
   *
   * @return mixed
   *   Query result.
   */
  public function getFieldValueByIds($entity_id, $field_name, $entity_type = 'node', $delta = 0) {
    $entity_storage = $this->entityTypeManager->getStorage($entity_type);
    $field_storage_definitions = $this->entityFieldManager->getFieldStorageDefinitions($entity_type);
    $definition = $field_storage_definitions[$field_name];
    $is_base = $definition->isBaseField();
    $table_mapping = $entity_storage->getTableMapping($field_storage_definitions);
    $table = $table_mapping->getFieldTableName($field_name);
    $table_columns = $table_mapping->getAllColumns($table);
    $entity_id = (array) $entity_id;
    $query = $this->database->select($table, 't');
    if (in_array('delta', $table_columns)) {
      $query->condition('delta', $delta);
    }
    if ($is_base) {
      $keys = $this->entityTypeManager->getDefinition($entity_type)->getKeys();
      $query->fields('t', [$field_name]);
      $query->condition($keys['id'], $entity_id, 'IN');
    }
    else {
      $columns = array_keys($definition->getSchema()['columns']);
      $value_key = reset($columns);
      $field_mapping = $table_mapping->getFieldColumnName($definition, $value_key);
      $query->fields('t', [$field_mapping]);
      $query->condition('entity_id', $entity_id, 'IN');
    }

    return $query->execute()->fetchField();
  }
  
  /**
   * A hack to empty a field value without a Node::save().
   *
   * To be used for speed critical cases, use Node::save() otherwise.
   * Will not handle cache invalidation.
   *
   * @param int|array $entity_id
   *   Entity id/ids.
   * @param string $field_name
   *   Name of the field.
   * @param string $entity_type
   *   Entity type machine string, like "node".
   *
   * @return int
   *   Number of deleted rows.
   */
  public function emptyFieldDb($entity_id, $field_name, $entity_type = 'node') {
    $entity_storage = $this->entityTypeManager->getStorage($entity_type);
    $field_storage_definitions = $this->entityFieldManager->getFieldStorageDefinitions($entity_type);
    $definition = $field_storage_definitions[$field_name];
    $is_base = $definition->isBaseField();
    $table_mapping = $entity_storage->getTableMapping($field_storage_definitions);
    $table = $table_mapping->getFieldTableName($field_name);
    $table_columns = $table_mapping->getAllColumns($table);
    $entity_id = (array) $entity_id;

    // Do not delete base field values.
    if ($is_base) {
      throw new \Exception('Will not delete the values of the base fields directly.');
    }

    // Do not delete from incompatible fields.
    if (!in_array('entity_id', $table_columns)) {
      throw new \Exception('The specified field type can not be emptied directly.');
    }

    return $this->database->delete($table)
      ->condition('entity_id', $entity_id, 'IN')
      ->execute();
  }

}
