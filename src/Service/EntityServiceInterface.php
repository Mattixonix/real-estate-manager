<?php

namespace Drupal\re_mgr\Service;

use Drupal\re_mgr\Entity\EntityInterface;
use Drupal\re_mgr\Entity\EntityTypeInterface;

/**
 * Interface for EntityService.
 */
interface EntityServiceInterface {

  /**
   * Get entity keyword from current route.
   *
   * @return string|null
   *   The entity keyword.
   */
  public function getCurrentEntityKeyword(): ?string;

  /**
   * Get entity keyword from string.
   *
   * @param string $entity_type_id
   *   The entity type id.
   *
   * @return string|null
   *   The entity keyword.
   */
  public function getEntityKeywordFromString(string $entity_type_id): ?string;

  /**
   * Get Real Estate Manager Entity from name and id.
   *
   * @param string $entity_keyword
   *   The entity keyword.
   * @param string $entity_id
   *   The entity id.
   *
   * @return \Drupal\re_mgr\Entity\EntityInterface|null
   *   The entity keyword.
   */
  public function getEntityFromData(string $entity_keyword, string $entity_id): ?EntityInterface;

  /**
   * Get related entities ids.
   *
   * Return ids for content entity it's children entities or for config entity
   * it's related content entities.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface|\Drupal\re_mgr\Entity\EntityTypeInterface $entity
   *   The entity or entity type.
   *
   * @return array
   *   The array of related entities ids.
   */
  public function getRelatedEntitiesIds(EntityInterface|EntityTypeInterface $entity): array;

  /**
   * Get related entities.
   *
   * Return entities for content entity it's children entities or for config
   * entity it's related content entities.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface|\Drupal\re_mgr\Entity\EntityTypeInterface $entity
   *   The entity or entity type.
   *
   * @return array
   *   The array of related entities.
   */
  public function getRelatedEntities(EntityInterface|EntityTypeInterface $entity): array;

  /**
   * Get information of related entities.
   *
   * @param \Drupal\re_mgr\Entity\EntityInterface|\Drupal\re_mgr\Entity\EntityTypeInterface $entity
   *   The entity or entity type.
   *
   * @return array
   *   The array of related entities info.
   */
  public function relatedEntitiesInfo(EntityInterface|EntityTypeInterface $entity): array;

}
