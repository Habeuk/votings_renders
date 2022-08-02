<?php

namespace Drupal\votings_renders\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'votings_renders_type' field type.
 *
 * @FieldType(
 *   id = "votings_renders_type",
 *   label = @Translation("Votings renders type"),
 *   description = @Translation("My Field Type"),
 *   default_widget = "votings_renders_widgedt_type",
 *   default_formatter = "votings_renders_formatter_type"
 * )
 */
class VotingsRendersType extends FieldItemBase {
  
  /**
   *
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 255,
      'is_ascii' => FALSE,
      'case_sensitive' => FALSE
    ] + parent::defaultStorageSettings();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // Prevent early t() calls by using the TranslatableMarkup.
    $properties['value'] = DataDefinition::create('string')->setLabel(new TranslatableMarkup('Text value'))->setSetting('case_sensitive', $field_definition->getSetting('case_sensitive'))->setRequired(TRUE);
    
    return $properties;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => 'int',
          'default' => 0,
          'description' => ' '
        ]
      ]
    ];
    
    return $schema;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $values['value'] = rand(0, 5);
    return $values;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $elements = [];
    
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }
  
}
