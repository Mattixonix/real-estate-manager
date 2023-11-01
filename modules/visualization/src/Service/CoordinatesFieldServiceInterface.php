<?php

namespace Drupal\re_mgr_visualization\Service;

/**
 * Provides interface for CoordinatesFieldService.
 */
interface CoordinatesFieldServiceInterface {

  /**
   * Create coordinates field.
   *
   * Create coordinates field for all bundles of all Real Estate Manager
   * entities except Estate.
   */
  public function createCoordinatesField(): void;

  /**
   * Delete coordinates field.
   *
   * Delete coordinates field for all bundles of all Real Estate Manager
   * entities.
   */
  public function deleteCoordinatesField(): void;

}
