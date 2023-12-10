<?php

namespace Drupal\re_mgr_presentation\Service;

/**
 * Provide interface for PresentationBlockBuilderService.
 */
interface PresentationBlockBuilderServiceInterface {

  /**
   * Returns block active tabs.
   *
   * @param array $plugins_labels
   *   Plugins labels.
   * @param array $plugins_configuration
   *   Plugins configuration.
   *
   * @return array
   *   The block tabs.
   */
  public function getBlockTabs(array $plugins_labels, array $plugins_configuration): array;

  /**
   * Returns first active content.
   *
   * @param array $plugins_content
   *   Plugins content.
   * @param array $plugins_configuration
   *   Plugins configuration.
   *
   * @return array
   *   The block content.
   */
  public function getBlockContent(array $plugins_content, array $plugins_configuration): array;

  /**
   * Returns sorted plugins.
   *
   * @param array $plugins_order
   *   Plugins order.
   * @param array $plugins
   *   The plugins.
   * @param array $plugin_labels
   *   Plugins labels.
   * @param array $plugin_content
   *   Plugin content.
   *
   * @return array
   *   Sorted plugins.
   */
  public function getSortedPlugins(array $plugins_order, array $plugins, array $plugin_labels, array $plugin_content): array;

}
