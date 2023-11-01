<?php

namespace Drupal\re_mgr\Entity;

use Drupal\Core\Entity\EntityDescriptionInterface;
use Drupal\Core\Entity\RevisionableEntityBundleInterface;

/**
 * Defines the interface for Real Estate Manager module entity types.
 */
interface EntityTypeInterface extends EntityDescriptionInterface, RevisionableEntityBundleInterface {

  /**
   * Sets whether a new revision should be created by default.
   */
  public function setNewRevision(bool $new_revision): EntityTypeInterface;

  /**
   * Get entity type keyword.
   */
  public function getEntityKeyword(): string;

}
