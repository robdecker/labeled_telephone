<?php

namespace Drupal\labeled_telephone\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\telephone\Plugin\Field\FieldType\TelephoneItem;

/**
 * Plugin implementation of the 'labeled_telephone' field type.
 *
 * @FieldType (
 *   id = "labeled_telephone",
 *   label = @Translation("Telephone number (labeled)"),
 *   description = @Translation("Stores a phone and label."),
 *   category = @Translation("Number"),
 *   default_widget = "labeled_telephone_default",
 *   default_formatter = "labeled_telephone_link"
 * )
 */
class LabeledTelephoneItem extends TelephoneItem {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = parent::propertyDefinitions($field_definition);

    $properties['label'] = DataDefinition::create('string')
      ->setLabel(t('Label'))
      ->setDescription(t('The Label'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = parent::schema($field_definition);

    $schema['columns']['label'] = array(
      'type' => 'varchar',
      'length' => 256,
    );
    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $parent = parent::isEmpty();
    $value = $this->get('label')->getValue();
    return $parent && empty($value);
  }

}
