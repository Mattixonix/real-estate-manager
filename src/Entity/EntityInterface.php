<?php

namespace Drupal\re_mgr\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a Real Estate Manager entities.
 */
interface EntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, RevisionLogInterface, EntityPublishedInterface {

  /**
   * Get parent entity type id.
   */
  public function getParentEntityTypeId(): ?string;

  /**
   * Get related entity type id.
   */
  public function getRelatedEntityTypeId(): ?string;

  /**
   * Get entity keyword.
   */
  public function getEntityKeyword(): string;

  /**
   * Get entity keyword from entity type id.
   */
  public function getEntityKeywordFromEntityTypeId(string $entity_type_id): ?string;

  /**
   * Get parent entity.
   */
  public function getParentEntity(): ?EntityInterface;

}
