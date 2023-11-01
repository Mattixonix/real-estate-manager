<?php

namespace Drupal\re_mgr\Form\Entity;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Real Estate Manager entities.
 */
class EntityDeleteForm extends ContentEntityDeleteForm {

  /**
   * The Entity service.
   */
  protected EntityServiceInterface $entityService;

  /**
   * Constructs a EntityDeleteForm object.
   */
  public function __construct(
    EntityRepositoryInterface $entity_repository,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    TimeInterface $time,
    EntityServiceInterface $entity_service
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): EntityDeleteForm {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('re_mgr.entity_service')
    );
  }

  /**
   * {@inheritdoc}
   *
   * Extended function entity delete, if it has any related entities.
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    /** @var \Drupal\re_mgr\Entity\EntityTypeInterface */
    $entity = $this->entity;
    $entity_info = [];

    if ($entity->getEntityTypeId() !== 're_mgr_flat') {
      $entity_info = $this->entityService->relatedEntitiesInfo($entity);
    }

    /* Check if there is any related entities */
    if (isset($entity_info['ids_number']) && $entity_info['ids_number'] !== 0) {
      $form = [
        'error_related_entities' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('%type entity is used by @count %related. You can not remove this %type entity until you have removed all of the %related content.', [
            '%type' => $entity_info['type'],
            '@count' => $entity_info['ids_number'],
            '%related' => $entity_info['related'],
          ]),
        ],
      ];
    }

    if (empty($entity_info) || $entity_info['ids_number'] === 0) {
      $form = parent::buildForm($form, $form_state);
    }

    return $form;
  }

}
