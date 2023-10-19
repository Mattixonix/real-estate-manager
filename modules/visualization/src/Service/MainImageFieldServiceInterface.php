<?php

namespace Drupal\re_mgr_visualization\Service;

/**
 * Provide interface for MainImageFieldService.
 */
interface MainImageFieldServiceInterface {

  /**
   * Create main image field.
   *
   * Create main image field for all bundles of all Real estate manager
   * entities.
   */
  public function createMainImageField(): void;

  /**
   * Delete main image field.
   *
   * Delete main image field for all bundles of all Real estate manager
   * entities.
   */
  public function deleteMainImageField(): void;

}
