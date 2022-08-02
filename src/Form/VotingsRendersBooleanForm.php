<?php

namespace Drupal\votings_renders\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\votingapi\Entity\Vote;
use Drupal\votingapi_reaction\Plugin\Field\FieldType\VotingApiReactionItemInterface;
use Drupal\votingapi_reaction\VotingApiReactionManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VotingsRendersBooleanForm extends ContentEntityForm {
  
  /**
   * The entity being used by this form.
   *
   * @var \Drupal\votingapi\Entity\Vote
   */
  protected $entity;
  
  /**
   * Current user service.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;
  
  public function __construct(AccountProxy $current_user, EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->currentUser = $current_user;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('current_user'), $container->get('entity.repository'), $container->get('entity_type.bundle.info'), $container->get('datetime.time'));
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function getFormId() {
    /* @var \Drupal\votingapi\Entity\Vote $entity */
    $entity = $this->getEntity();
    
    return implode('_', [
      'votings_renders',
      $entity->getVotedEntityType(),
      $entity->getVotedEntityId()
    ]);
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $form['#id'] = Html::getUniqueId('votings_renders_form');
    $entity = $this->entity;
    $value = $entity->get('value')->value;
    $form['#attributes']['class'][] = 'form-votings-renders';
    if ($form_state->hasValue('value')) {
      $value = $form_state->getValue('value');
    }
    
    // Display reactions.
    $form['value'] = [
      '#type' => 'radios',
      '#title' => '',
      '#attributes' => [
        'class' => [
          'svg-icones'
        ],
        'data-twig' => 'clean'
      ],
      '#options' => [
        '5' => '',
        '4' => '',
        '3' => '',
        '2' => '',
        '1' => ''
      ],
      '#default_value' => $value,
      '#id' => $form['#id'] . '-vote',
      '#ajax' => [
        'callback' => [
          $this,
          'ajaxSubmitForm'
        ],
        'event' => 'click',
        'wrapper' => $form['#id'],
        'progress' => [
          'type' => NULL,
          'message' => NULL
        ]
      ]
    ];
    $form['actions']['#access'] = false;
    // dump($form);
    return $form;
  }
  
  /**
   * Ajax submit handler.
   *
   * @param array $form
   *        The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *        The form state.
   *        
   * @return array Return form.
   */
  public function ajaxSubmitForm(array $form, FormStateInterface $form_state) {
    $this->messenger()->addStatus('ajaxSubmitForm');
    $this->submitForm($form, $form_state);
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // If new reaction was selected.
    $trigger = $form_state->getTriggeringElement();
    if (!empty($trigger['#type']) && $trigger['#type'] == 'radio') {
      parent::submitForm($form, $form_state);
      parent::save($form, $form_state);
    }
    //
  }
  
}