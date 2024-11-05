<?php

namespace Drupal\butils\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'json_metadata_preview' field widget.
 *
 * @FieldWidget(
 *   id = "json_metadata_preview",
 *   label = @Translation("JSON Metadata Preview"),
 *   field_types = {
 *      "json_metadata"
 *    }
 * )
 */
class JsonMetadataPreviewWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = $items[0]->value ?: '{}';
    $element['value'] = [
      'container' => [
        '#type' => 'details',
        '#title' => $this->t('JSON Metadata'),
        '#open' => FALSE,
        'value' => [
          '#markup' => '<code>' . $value . '</code>',
        ],
      ],
    ];

    return $element;
  }

}
