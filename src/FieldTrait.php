<?php

namespace Drupal\butils;

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

}
