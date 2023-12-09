<?php

namespace Drupal\re_mgr\Form\Entity;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to delete Real Estate Manager entity type.
 */
class EntityTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * The Entity service.
   *
   * @var \Drupal\re_mgr\Service\EntityServiceInterface
   */
  protected EntityServiceInterface $entityService;

  /**
   * Constructs a EntityTypeDeleteForm object.
   *
   * @param \Drupal\re_mgr\Service\EntityServiceInterface $entity_service
   *   The Entity service.
   */
  public function __construct(protected EntityServiceInterface $entity_service) {
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): EntityTypeDeleteForm {
    return new static(
      $container->get('re_mgr.entity_service')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Extended function block entity delete, if it has any related entities.
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    /** @var \Drupal\re_mgr\Entity\EntityTypeInterface */
    $entity = $this->entity;
    $entity_type_info = [];

    if ($entity->getEntityTypeId() !== 're_mgr_flat') {
      $entity_type_info = $this->entityService->relatedEntitiesInfo($entity);
    }

    if (empty($entity_type_info) || $entity_type_info['ids_number'] === 0) {
      $form = parent::buildForm($form, $form_state);
    }
    else {
      $form = [
        '#markup' => $this->t('%type entity type is used by @count %related. You can not remove this %type entity type until you have removed all of the %related content.', [
          '%type' => $entity_type_info['type'],
          '@count' => $entity_type_info['ids_number'],
          '%related' => $entity_type_info['related'],
        ]),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.re_mgr_' . $this->entityService->getCurrentEntityKeyword() . '_type.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->entity->delete();
    $this->messenger()->addMessage($this->t('Entity type %label has been deleted.', [
      '%label' => $this->entity->label(),
    ]));

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
