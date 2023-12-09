<?php

namespace Drupal\re_mgr\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Base class for Realestate Manager entity types.
 */
abstract class EntityTypeBase extends ConfigEntityBundleBase implements EntityTypeInterface {

  /**
   * The EstateType ID.
   *
   * @var string
   */
  protected string $id;

  /**
   * The EstateType label.
   *
   * @var string
   */
  protected string $label;

  /**
   * The EstateType description.
   *
   * @var string
   */
  protected string $description = '';

  /**
   * Default value of the 'Create new revision' checkbox of Estate type.
   *
   * @var bool
   */
  protected bool $new_revision = TRUE;

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description): EntityTypeInterface {
    $this->description = $description;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateNewRevision() {
    return $this->new_revision;
  }

  /**
   * {@inheritdoc}
   */
  public function setNewRevision(bool $new_revision): EntityTypeInterface {
    $this->new_revision = $new_revision;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityKeyword(): string {
    return explode('_', $this->getEntityTypeId())[2];
  }

}
