<?php

namespace Drupal\re_mgr\Entity;

/**
 * Provide base const data for operations on Real Estate Manager entities.
 */
trait EntityBaseDataTrait {

  /**
   * Entity list.
   */
  public const ENTITY_LIST = [
    're_mgr_estate',
    're_mgr_building',
    're_mgr_floor',
    're_mgr_flat',
  ];

  /**
   * Map of parent entity names.
   */
  protected const PARENT_ENTITY_MAP = [
    're_mgr_flat' => 're_mgr_floor',
    're_mgr_floor' => 're_mgr_building',
    're_mgr_building' => 're_mgr_estate',
  ];

  /**
   * Map of related entity names.
   */
  protected const RELATED_ENTITY_MAP = [
    're_mgr_estate' => 're_mgr_building',
    're_mgr_building' => 're_mgr_floor',
    're_mgr_floor' => 're_mgr_flat',
  ];

  /**
   * Contains all flat statuses.
   */
  public const ENTITIES_STATUSES = [
    1 => 'Available',
    2 => 'Reserved',
    3 => 'Sold',
  ];

  /**
   * Contains statuses colors.
   */
  public const STATUSES_COLOR = [
    1 => 'green',
    2 => 'orange',
    3 => 'red',
  ];

}
