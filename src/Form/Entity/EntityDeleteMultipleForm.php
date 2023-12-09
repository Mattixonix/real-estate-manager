<?php

namespace Drupal\re_mgr\Form\Entity;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Form\DeleteMultipleForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\re_mgr\Entity\Flat\Flat;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an entities deletion confirmation form.
 */
class EntityDeleteMultipleForm extends DeleteMultipleForm {

  /**
   * The Entity service.
   *
   * @var \Drupal\re_mgr\Service\EntityServiceInterface
   */
  protected EntityServiceInterface $entityService;

  /**
   * Constructs a new EntityDeleteMultipleForm object.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\re_mgr\Service\EntityServiceInterface $entity_service
   *   The Entity service.
   */
  public function __construct(
    AccountInterface $current_user,
    EntityTypeManagerInterface $entity_type_manager,
    PrivateTempStoreFactory $temp_store_factory,
    MessengerInterface $messenger,
    EntityServiceInterface $entity_service
  ) {
    parent::__construct($current_user, $entity_type_manager, $temp_store_factory, $messenger);
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): EntityDeleteMultipleForm {
    return new static(
      $container->get('current_user'),
      $container->get('entity_type.manager'),
      $container->get('tempstore.private'),
      $container->get('messenger'),
      $container->get('re_mgr.entity_service')
    );
  }

  /**
   * Returns a selection list.
   *
   * @return array
   *   Array of selected items.
   */
  public function getSelection(): array {
    return $this->selection;
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   * @param string $entity_type_id
   *   The entity type id.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL): array {
    /** @var array */
    $selection = $this->tempStore->get($this->currentUser->id() . ':' . $entity_type_id);
    /** @var string $entity_type_id */
    $entities = $this->entityTypeManager
      ->getStorage($entity_type_id)
      ->loadMultiple(array_keys($selection));
    $any_related_entities = FALSE;
    $type = '';

    /** @var \Drupal\re_mgr\Entity\EntityInterface $entity */
    foreach ($entities as $entity) {
      if ($entity instanceof Flat) {
        break;
      }

      $entity_info = $this->entityService->relatedEntitiesInfo($entity);

      if ($entity_info['ids_number']) {
        $any_related_entities = TRUE;
        $type = $entity_info['type'];
        break;
      }
    }

    if (!$any_related_entities) {
      $form = parent::buildForm($form, $form_state, $entity_type_id);
    }
    else {
      $title = parent::buildForm($form, $form_state, $entity_type_id)['#title'];
      $form['#title'] = $title;
      $form['error_related_entities'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('At least one selected %type entity is used. You need to delete related content first.', [
          '%type' => $type,
        ]),
      ];
    }

    return $form;
  }

}
