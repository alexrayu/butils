<?php

namespace Drupal\butils\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'json_metadata' field type.
 *
 * @FieldType(
 *   id = "json_metadata",
 *   label = @Translation("JSON Metadata"),
 *   description = @Translation("Stores JSON-encoded metadata for the entity. Suggested field name is <i>field_json_metadata</i>."),
 *   category = @Translation("Metadata"),
 *   default_widget = "json_metadata",
 *   default_formatter = "json_metadata",
 *   cardinality = 1,
 * )
 */
class JsonMetadataItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public function get($property_name) {
    if (!isset($this->properties[$property_name])) {
      $value = NULL;
      if (isset($this->values[$property_name])) {
        $value = $this->values[$property_name];
      }
      // If the property is unknown, this will throw an exception.
      $this->properties[$property_name] = $this->getTypedDataManager()->getPropertyInstance($this, $property_name, $value);
    }
    return $this->properties[$property_name];
  }

  /**
   * {@inheritdoc}
   */
  public function set($property_name, $value, $notify = TRUE) {
    // Separate the writing in a protected method, such that onChange
    // implementations can make use of it.
    $this->writePropertyValue($property_name, $value);
    $this->onChange($property_name, $notify);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return empty($value);
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Metadata JSON'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    return parent::getConstraints();
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'blob',
          'size' => 'big',
          'description' => 'The metadata JSON.',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $random = new Random();
    $values = [];
    $values['value'] = [
      'test' => $random->word(10),
    ];

    return $values;
  }

}
