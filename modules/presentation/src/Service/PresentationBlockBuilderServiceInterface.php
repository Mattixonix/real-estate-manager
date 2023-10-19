<?php

namespace Drupal\re_mgr_presentation\Service;

/**
 * Provide interface for PresentationBlockBuilderService.
 */
interface PresentationBlockBuilderServiceInterface {

  /**
   * Returns block active tabs.
   */
  public function getBlockTabs(array $plugins_labels, array $plugins_configuration): array;

  /**
   * Returns first active content.
   */
  public function getBlockContent(array $plugins_content, array $plugins_configuration): array;

  /**
   * Returns sorted plugins.
   */
  public function getSortedPlugins(array $plugins_order, array $plugins, array $plugin_labels, array $plugin_content): array;

}
