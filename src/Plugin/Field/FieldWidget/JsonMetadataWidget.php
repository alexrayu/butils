<?php

namespace Drupal\butils\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'json_metadata' field widget.
 *
 * @FieldWidget(
 *   id = "json_metadata",
 *   label = @Translation("JSON Metadata"),
 *   field_types = {
 *      "json_metadata"
 *    }
 * )
 */
class JsonMetadataWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['value'] = $element + [
      '#type' => 'textarea',
      '#default_value' => $items[0]->value ?? NULL,
    ];

    return $element;
  }

}
