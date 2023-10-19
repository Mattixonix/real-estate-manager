<?php

namespace Drupal\re_mgr\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\re_mgr\Entity\EntityBaseDataTrait;
use Drupal\re_mgr\Entity\EntityInterface;
use Drupal\re_mgr\Entity\EntityTypeInterface;
use Drupal\re_mgr\Entity\Flat\Flat;

/**
 * The entity service.
 */
class EntityService implements EntityServiceInterface {
  use EntityBaseDataTrait;

  /**
   * The Entity Type Manager service.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The Current Route Match revision.
   */
  protected RouteMatchInterface $currentRouteMatch;

  /**
   * Constructs a EntityService object.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    RouteMatchInterface $current_route_match
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrentEntityKeyword(): ?string {
    $entity_key_word = NULL;
    /** @var string */
    $current_route_name = $this->currentRouteMatch->getRouteName();

    if (str_starts_with($current_route_name, 'entity.re_mgr_')) {
      $entity_type_id = explode('.', $current_route_name)[1];
      $entity_key_word = explode('_', $entity_type_id)[2];
    }

    return $entity_key_word;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityKeywordFromString(string $entity_type_id): ?string {
    $entity_key_word = NULL;

    if (str_starts_with($entity_type_id, 're_mgr_')) {
      $entity_key_word = explode('_', $entity_type_id)[2];
    }

    return $entity_key_word;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFromData(string $entity_keyword, string $entity_id): ?EntityInterface {
    /** @var \Drupal\re_mgr\Entity\EntityInterface|null */
    $entity = $this->entityTypeManager->getStorage('re_mgr_' . $entity_keyword)->load($entity_id);

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelatedEntitiesIds(EntityInterface|EntityTypeInterface $entity): array {
    if ($entity instanceof Flat) {
      return [];
    }

    $storage_name = $this->getStorageName($entity);
    $field_name = $this->getQueryFieldName($entity);

    /** @var array */
    $ids = $this->entityTypeManager
      ->getStorage($storage_name)
      ->getQuery()
      ->accessCheck(TRUE)
      ->condition($field_name, $entity->id())
      ->execute();

    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelatedEntities(EntityInterface|EntityTypeInterface $entity): array {
    if ($entity instanceof Flat) {
      return [];
    }

    $storage_name = $this->getStorageName($entity);
    $ids = $this->getRelatedEntitiesIds($entity);
    $entities = $this->entityTypeManager
      ->getStorage($storage_name)
      ->loadMultiple($ids);

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function relatedEntitiesInfo(EntityInterface|EntityTypeInterface $entity): array {
    if ($entity instanceof Flat) {
      $entity_info = [];
    }

    $entity_type_id = $entity->getEntityTypeId();
    $entity_keyword = $entity->getEntityKeyword();
    /** @var string|null */
    $related_entity_id = self::RELATED_ENTITY_MAP[$entity_type_id];
    $related = $this->getEntityKeywordFromString($entity_type_id);

    if (!empty($related_entity_id)) {
      $related = $this->getEntityKeywordFromString(self::RELATED_ENTITY_MAP[$entity_type_id]);
    }

    /** @var array */
    $ids = $this->getRelatedEntitiesIds($entity);

    if (count($ids) > 1) {
      $related .= 's';
    }

    $entity_info = [
      'type' => ucfirst($entity_keyword),
      'ids_number' => count($ids),
      'related' => $related,
    ];

    return $entity_info;
  }

  /**
   * Get storage name base on given entity.
   */
  protected function getStorageName(EntityInterface|EntityTypeInterface $entity): string {
    $entity_type_id = $entity->getEntityTypeId();
    $storage_name = self::RELATED_ENTITY_MAP[$entity_type_id];

    if (str_ends_with($entity_type_id, '_type')) {
      $storage_name = substr($entity_type_id, 0, -5);
    }

    return $storage_name;
  }

  /**
   * Get field name base on given entity.
   */
  protected function getQueryFieldName(EntityInterface|EntityTypeInterface $entity): string {
    $field_name = $entity->getEntityKeyword() . '_id';

    if (str_ends_with($entity->getEntityTypeId(), '_type')) {
      $field_name = 'type';
    }

    return $field_name;
  }

}
