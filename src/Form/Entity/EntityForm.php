<?php

namespace Drupal\re_mgr\Form\Entity;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for adding and editing a Real Estate Manager entities.
 */
class EntityForm extends ContentEntityForm {

  /**
   * The Date Formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * Constructs a new EntityForm object.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The Module Handler service.
   */
  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
    DateFormatterInterface $date_formatter
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): EntityForm {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $form['#theme'] = ['entity_form'];
    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 're_mgr/entity-form';

    /* Advanced sidebar. */
    $form['advanced'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['entity-meta']],
      '#weight' => 99,
    ];

    /* Entity meta data */
    /** @var \Drupal\re_mgr\Entity\EntityInterface */
    $entity = $this->entity;
    $form['meta'] = [
      '#attributes' => ['class' => ['entity-meta__header']],
      '#type' => 'container',
      '#group' => 'advanced',
      '#weight' => -100,
    ];
    if (isset($entity->status->value)) {
      $status = $form['status']['widget']['#options'][$entity->status->value];
      $form['meta']['published'] = [
        '#type' => 'html_tag',
        '#tag' => 'h3',
        '#value' => $status,
        '#attributes' => [
          'class' => ['entity-meta__title'],
        ],
      ];
    }
    $last_saved = $this->t('Not saved yet');
    if (!$entity->isNew()) {
      $last_saved = $this->dateFormatter->format($entity->getChangedTime(), 'short');
    }
    $form['meta']['changed'] = [
      '#type' => 'item',
      '#wrapper_attributes' => [
        'class' => ['entity-meta__last-saved', 'container-inline'],
      ],
      '#markup' => '<h4 class="label inline">' . $this->t('Last saved') . '</h4> ' . $last_saved,
    ];
    $form['meta']['author'] = [
      'author' => [
        '#type' => 'item',
        '#wrapper_attributes' => [
          'class' => ['author', 'container-inline'],
        ],
        '#markup' => '<h4 class="label inline">' . $this->t('Author') . '</h4> ' . $entity->getOwner()->getDisplayName(),
      ],
    ];

    /* Revision settings. */
    $form['revision_information']['#open'] = TRUE;

    /* Entity author information for administrators. */
    $form['authoring_information'] = [
      '#type' => 'details',
      '#title' => $this->t('Authoring information'),
      '#group' => 'advanced',
      '#weight' => 90,
      '#optional' => TRUE,
    ];
    if (isset($form['uid'])) {
      $form['uid']['#group'] = 'authoring_information';
    }
    if (isset($form['created'])) {
      $form['created']['#group'] = 'authoring_information';
    }

    /* Footer section. */
    $form['published']['#group'] = 'footer';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    /** @var \Drupal\re_mgr\Entity\EntityInterface */
    $entity = $this->entity;
    $redirect_route = "entity.{$entity->getEntityTypeId()}.collection";

    if ($status == SAVED_UPDATED) {
      $this->messenger()->addMessage($this->t('The @entity %feed has been updated.', [
        '@entity' => $entity->getEntityKeyword(),
        '%feed' => $this->entity->toLink()->toString(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('The @entity %feed has been added.', [
        '@entity' => $entity->getEntityKeyword(),
        '%feed' => $this->entity->toLink()->toString(),
      ]));
    }

    $form_state->setRedirect($redirect_route);

    return $status;
  }

}
