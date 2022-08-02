<?php

namespace Drupal\votings_renders\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\votingapi\Entity\Vote;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\votingapi\VoteResultFunctionManager;

/**
 * Plugin implementation of the 'votings_renders_formatter_type' formatter.
 *
 * @FieldFormatter(
 *   id = "votings_renders_formatter_type",
 *   label = @Translation("Votings renders formatter type"),
 *   field_types = {
 *     "votings_renders_type"
 *   }
 * )
 */
class VotingsRendersFormatterType extends FormatterBase implements ContainerFactoryPluginInterface {
  
  /**
   * Form builder service.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilder
   */
  protected $formBuilder;
  
  /**
   *
   * @var VoteResultFunctionManager
   */
  protected $VoteResultFunctionManager;
  
  /**
   * Constructs an VotingApiReactionFormatter object.
   *
   * @param string $plugin_id
   *        The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *        The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *        The definition of the field to which the formatter is associated.
   * @param array $settings
   *        The formatter settings.
   * @param string $label
   *        The formatter label display setting.
   * @param string $view_mode
   *        The view mode.
   * @param array $third_party_settings
   *        Any third party settings settings.
   * @param \Drupal\Core\Entity\EntityFormBuilder $form_builder
   *        Form builder service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityFormBuilder $form_builder, VoteResultFunctionManager $VoteResultFunctionManager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->formBuilder = $form_builder;
    $this->VoteResultFunctionManager = $VoteResultFunctionManager;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($plugin_id, $plugin_definition, $configuration['field_definition'], $configuration['settings'], $configuration['label'], $configuration['view_mode'], $configuration['third_party_settings'], $container->get('entity.form_builder'), $container->get('plugin.manager.votingapi.resultfunction'));
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [ // Implement default settings.
    ] + parent::defaultSettings();
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [ // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    return $summary;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    // dump($items);
    $results = $this->VoteResultFunctionManager->getResults($items->getEntity()->getEntityTypeId(), $items->getEntity()->id());
    // dump($results);
    $votings_renders_note = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => 'Donner votre avis !'
    ];
    if (!empty($results['votings_renders_note']['vote_average'])) {
      $votings_renders_note['#value'] = $results['votings_renders_note']['vote_average'] . '/5';
    }
    $extras = [];
    $form = $this->formBuilder->getForm($this->getVoteEntity($items), 'votings_renders', $extras);
    $elements[] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'd-flex',
          'justify-content-left',
          'align-items-center'
        ]
      ],
      'form' => $form,
      'vote_average' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#attributes' => [
          'class' => [
            'voting-text'
          ]
        ],
        [
          $votings_renders_note
        ]
      ]
    ];
    
    return $elements;
  }
  
  /**
   * Recupere le vote de l'utilisateur (s'il a deja vote) ou genere l'entité.
   *
   * @param FieldItemListInterface $items
   */
  protected function getVoteEntity(FieldItemListInterface $items) {
    $entity = $items->getEntity();
    $user_id = \Drupal::currentUser()->id();
    //
    if ($user_id) {
      $query = \Drupal::entityTypeManager()->getStorage('vote')->getQuery();
      $query->condition('type', 'votings_renders_note');
      $query->condition('user_id', \Drupal::currentUser()->id());
      $query->condition('entity_id', $entity->id());
      $query->condition('entity_type', $entity->getEntityTypeId());
      $ids = $query->execute();
      if (!empty(($ids))) {
        $votes = \Drupal::entityTypeManager()->getStorage('vote')->loadMultiple($ids);
        return reset($votes);
      }
    }
    //
    return Vote::create([
      'type' => 'votings_renders_note',
      'entity_id' => $items->getEntity()->id(),
      'entity_type' => $items->getEntity()->getEntityTypeId(),
      'value_type' => 'option',
      'value' => 0
    ]);
  }
  
}
