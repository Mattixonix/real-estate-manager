<?php

namespace Drupal\re_mgr\Form\Entity;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\re_mgr\Entity\EntityTypeInterface;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to create Real Estate Manager entity type.
 */
class EntityTypeForm extends BundleEntityFormBase {

  /**
   * Current entity type.
   *
   * @var \Drupal\re_mgr\Entity\EntityTypeInterface
   */
  protected EntityTypeInterface $entityType;

  /**
   * The Entity service.
   *
   * @var \Drupal\re_mgr\Service\EntityServiceInterface
   */
  protected EntityServiceInterface $entityService;

  /**
   * The Display Repository service.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected EntityDisplayRepositoryInterface $displayRepository;

  /**
   * Constructs a EntityTypeForm object.
   *
   * @param \Drupal\re_mgr\Service\EntityServiceInterface $entity_service
   *   The Entity service.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $display_repository
   *   The Display Repository service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The Module Handler service.
   */
  public function __construct(
    EntityServiceInterface $entity_service,
    EntityDisplayRepositoryInterface $display_repository,
    ModuleHandlerInterface $module_handler
  ) {
    $this->entityService = $entity_service;
    $this->displayRepository = $display_repository;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): EntityTypeForm {
    return new static(
      $container->get('re_mgr.entity_service'),
      $container->get('entity_display.repository'),
      $container->get('module_handler'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    /** @var \Drupal\re_mgr\Entity\EntityTypeInterface */
    $entity_type = $this->entity;
    $this->entityType = $entity_type;

    $form = parent::form($form, $form_state);

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#disabled' => !$this->entity->isNew(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('This text will be displayed on the <em>Add %label</em> page.', [
        '%label' => $this->entityType->getEntityKeyword(),
      ]),
      '#default_value' => $this->entityType->getDescription(),
    ];
    $form['new_revision'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Create new revision'),
      '#default_value' => $this->entityType->shouldCreateNewRevision(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();
    /** @var string */
    $bundle = $this->entity->id();
    $this->displayRepository->getFormDisplay('re_mgr_' . $this->entityType->getEntityKeyword(), $bundle)->save();
    $this->displayRepository->getViewDisplay('re_mgr_' . $this->entityType->getEntityKeyword(), $bundle)->save();
    $this->moduleHandler->invokeAll('entity_bundle_after_create', ['re_mgr_' . $this->entityType->getEntityKeyword()]);

    if ($status === SAVED_NEW) {
      $this->messenger()->addMessage($this->t('The %label %type type created.', [
        '%label' => $this->entity->label(),
        '%type' => $this->entityType->getEntityKeyword(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('The %label %type type updated.', [
        '%label' => $this->entity->label(),
        '%type' => $this->entityType->getEntityKeyword(),
      ]));
    }

    $form_state->setRedirect('entity.re_mgr_' . $this->entityType->getEntityKeyword() . '_type.collection');

    return $status;
  }

  /**
   * Check whether an entity type configuration exists.
   * 
   * @param string $id
   *  The string id.
   * 
   * @return bool
   *  Is id exists.
   */
  public function exist(string $id): bool {
    $entity = $this->entityTypeManager
      ->getStorage('re_mgr_' . $this->entityType->getEntityKeyword() . '_type')
      ->getQuery()
      ->accessCheck(TRUE)
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
