<?php

namespace Drupal\re_mgr\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler as CoreEntityAccessControlHandler;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\re_mgr\Service\EntityServiceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Access controller for the Real estate manager entities.
 */
class EntityAccessControlHandler extends CoreEntityAccessControlHandler implements EntityHandlerInterface {

  /**
   * The Real estate manager Entity Service.
   */
  protected EntityServiceInterface $entityService;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): EntityAccessControlHandler {
    return new static(
      $entity_type,
      $container->get('re_mgr.entity_service')
    );
  }

  /**
   * Constructs the EntityAccessControlHandler instance.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityServiceInterface $entity_service) {
    parent::__construct($entity_type);
    $this->entityService = $entity_service;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    /** @var \Drupal\re_mgr\Entity\EntityInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view ' . $entity->getEntityKeyword() . ' entity');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit ' . $entity->getEntityKeyword() . ' entity');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete ' . $entity->getEntityKeyword() . ' entity');
    }
    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermission($account, 'add ' . $this->entityService->getCurrentEntityKeyword() . ' entity');
  }

}
