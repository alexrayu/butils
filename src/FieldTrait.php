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
      $definitions[$name] = [
        'field_name' => $name,
        'entity_type' => $entity_type,
        'bundle' => $bundle,
        'definition' => $definition,
      ];
      $definitions[$name]['details'] = $this->getFieldDefinitionsDetails($definitions[$name]);
    }

    return $extended_definitions;
  }

  /**
   * Gets the details of a filter.
   *
   * @param array $extended_definition
   *   Field's extended definition array.
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
  public function emptyField(EntityInterface &$entity, $field_name, $save = TRUE) {
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
   *
   * @return string
   *   Field value.
   */
  public function renderField(EntityInterface &$entity, $field_name, array $options = []) {
    $result = '';
    $options['display'] = $options['display'] ?? 'default';
    $options['multiple'] = $options['multiple'] ?? TRUE;
    if (!$entity instanceof FieldableEntityInterface
        || !$entity->hasField($field_name)) {
      return $result;
    }
    $field_item = $options['multiple']
      ? $entity->{$field_name}
      : $entity->{$field_name}->get(0);
    if (!empty($field_item)) {
      $definition = $field_item->getFieldDefinition();
      $field_type = $definition->get('field_type');
      $field_build = $field_item->view($options['display']);
      $field_build['#label_display'] = 'hidden';
      switch ($field_type) {
        case 'file':
        case 'image':
          $result = (string) $this->renderer->renderRoot($field_build);
          break;

        default:
          $result = strip_tags((string) $this->renderer->renderRoot($field_build));
      }
      $result = $this->cleanHtml($result);
    }
    return $result;
  }

  /**
   * A hack to get field value without a Node::load().
   *
   * To be used for speed critical cases, use Node::load() otherwise.
   *
   * @param int $entity_id
   *   Entity id.
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
    $table = $entity_type . '__' . $field_name;
    $row = $field_name . '_value';
    return $this->database->select($table, 't')
      ->condition('entity_id', $entity_id)
      ->condition('deleted', 0)
      ->condition('delta', $delta)
      ->fields('t', [$row])
      ->execute()
      ->fetchField();
  }

}
