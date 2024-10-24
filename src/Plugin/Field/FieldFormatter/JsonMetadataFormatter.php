<?php

namespace Drupal\butils\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'JSON Metadata' formatter.
 *
 * @FieldFormatter(
 *   id = "json_metadata",
 *   label = @Translation("JSON Metadata"),
 *   field_types = {
 *     "json_metadata"
 *   }
 * )
 */
class JsonMetadataFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    if (!empty($items[0])) {
      $element[0] = [
        '#markup' => '<code>' . $items[0]->value . '</code>',
      ];
    }

    return $element;
  }

}
