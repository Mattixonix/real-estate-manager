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
   */
  public function getCurrentEntityKeyword(): ?string;

  /**
   * Get entity keyword from string.
   */
  public function getEntityKeywordFromString(string $entity_type_id): ?string;

  /**
   * Get Real Estate Manager Entity from name and id.
   */
  public function getEntityFromData(string $entity_keyword, string $entity_id): ?EntityInterface;

  /**
   * Get related entities ids.
   *
   * Return ids for content entity it's children entities or for config entity
   * it's related content entities.
   */
  public function getRelatedEntitiesIds(EntityInterface|EntityTypeInterface $entity): array;

  /**
   * Get related entities.
   *
   * Return entities for content entity it's children entities or for config
   * entity it's related content entities.
   */
  public function getRelatedEntities(EntityInterface|EntityTypeInterface $entity): array;

  /**
   * Get information of related entities.
   */
  public function relatedEntitiesInfo(EntityInterface|EntityTypeInterface $entity): array;

}
