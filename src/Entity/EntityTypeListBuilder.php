<?php

namespace Drupal\re_mgr\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Real estate manager Estate types.
 */
class EntityTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Type');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\re_mgr\Entity\EntityTypeInterface $entity */
    $row['name'] = $entity->label();
    $row['description'] = $entity->getDescription();

    return $row + parent::buildRow($entity);
  }

}
