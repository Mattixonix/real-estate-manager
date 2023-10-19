<?php

namespace Drupal\re_mgr\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;

/**
 * Base class for Realestate Manager entities.
 */
abstract class EntityBase extends EditorialContentEntityBase implements EntityInterface {
  use EntityBaseDataTrait;

  /**
   * {@inheritdoc}
   */
  public function getParentEntityTypeId(): ?string {
    return self::PARENT_ENTITY_MAP[$this->getEntityTypeId()];
  }

  /**
   * {@inheritdoc}
   */
  public function getRelatedEntityTypeId(): ?string {
    return self::RELATED_ENTITY_MAP[$this->getEntityTypeId()];
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityKeyword(): string {
    return explode('_', $this->getEntityTypeId())[2];
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityKeywordFromEntityTypeId(string $entity_type_id): ?string {
    if (!str_starts_with($entity_type_id, 're_mgr_')) {
      return NULL;
    }

    return explode('_', $entity_type_id)[2];
  }

  /**
   * {@inheritdoc}
   */
  public function getParentEntity(): ?EntityInterface {
    if ($this->getEntityKeyword() === 'estate') {
      return NULL;
    }

    $parent_entity_field_name = $this->getEntityKeywordFromEntityTypeId(self::PARENT_ENTITY_MAP[$this->getEntityTypeId()]) . '_id';

    return $this->$parent_entity_field_name->entity;
  }

}
