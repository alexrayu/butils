<?php

namespace Drupal\butils;

use Drupal\paragraphs\Entity\Paragraph;

/**
 * Trait ParagraphsTrait.
 *
 * Paragraphs related utils.
 */
trait ParagraphsTrait {

  /**
   * Delete nested paragraphs recursively.
   *
   * @param array $field_values
   *   Field for recursive handling.
   */
  public function deleteParagraphsRecurively(array $field_values) {
    foreach ($field_values as $value) {
      if (!empty($value['target_id']) && !empty($value['target_id'])) {
        $paragraph = Paragraph::load($value['target_id']);
        if (empty($paragraph)) {
          continue;
        }
        $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($paragraph->getEntityTypeId(), $paragraph->bundle());
        foreach ($field_definitions as $field_definition) {
          if ($field_definition->getType() === 'entity_reference_revisions') {
            $values = $paragraph->get($field_definition->getName())->getValue();
            if (!empty($values)) {
              $this->deleteParagraphsRecurively($values);
            }
          }
        }
        $paragraph->delete();
      }
    }
  }

}
