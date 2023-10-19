<?php

namespace Drupal\re_mgr_demo;

/**
 * Creator of demo data.
 */
interface CreatorServiceInterface {

  /**
   * Initialize base data creator.
   */
  public function initBaseCreator(): void;

  /**
   * Initialize visualization data creator.
   */
  public function initVisualizationCreator(): void;

  /**
   * Remove all demo media entities.
   */
  public function removeAllDemoMedia(): void;

}
