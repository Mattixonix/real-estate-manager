<?php

namespace Drupal\re_mgr_presentation\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Block builder service.
 */
class PresentationBlockBuilderService implements PresentationBlockBuilderServiceInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getBlockTabs(array $plugins_labels, array $plugins_configuration): array {
    /* If there is one or less label, return empty. */
    if (count($plugins_labels) < 2) {
      return [];
    }

    $tabs = [];

    foreach ($plugins_labels as $plugin_key => $label) {
      $is_plugin_enabled = FALSE;

      if (isset($plugins_configuration[$plugin_key])) {
        $is_plugin_enabled = (bool) $plugins_configuration[$plugin_key]['switch'];
      }

      if ($is_plugin_enabled) {
        $tabs[$plugin_key] = [
          '#type' => 'link',
          '#title' => $label,
          '#url' => Url::fromRoute('re_mgr_presentation.presentation_change_tab', [
            'plugin_id' => $plugin_key,
            'block_id' => $plugins_configuration['block_id'],
          ]),
          '#attributes' => [
            'class' => [
              're-mgr-tab-btn',
              'use-ajax',
            ],
          ],
        ];

        if ($plugin_key === array_key_first($plugins_labels)) {
          $tabs[$plugin_key]['#attributes']['class'][] = 'active-tab';
        }
      }
    }

    /* If there is one or less active tab, return empty. */
    if (count($tabs) < 2) {
      return [];
    }

    return $tabs;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockContent(array $plugins_content, array $plugins_configuration): array {
    foreach ($plugins_content as $plugin_key => $content) {
      $is_plugin_enabled = FALSE;

      if (isset($plugins_configuration[$plugin_key])) {
        $is_plugin_enabled = (bool) $plugins_configuration[$plugin_key]['switch'];
      }

      if ($is_plugin_enabled) {
        return $plugins_content[$plugin_key];
      }
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getSortedPlugins(array $plugins_order, array $plugins, array $plugin_labels, array $plugin_content): array {
    $sorted_plugins = [
      'plugins' => [],
      'plugin_labels' => [],
      'plugin_content' => [],
    ];

    /* Sort plugins */
    foreach ($plugins as $plugin_id => $plugin) {
      $plugin_weight = $plugins_order[$plugin_id]['weight'];
      $sorted_plugins['plugins'][$plugin_weight][$plugin_id] = $plugin;
    }

    ksort($sorted_plugins['plugins']);

    foreach ($sorted_plugins['plugins'] as $weight => $plugin) {
      $plugin_id = array_key_first($plugin);
      unset($sorted_plugins['plugins'][$weight]);
      $sorted_plugins['plugins'][$plugin_id] = $plugin[$plugin_id];
    }

    /* Sort plugin labels */
    foreach ($plugin_labels as $plugin_id => $plugin_label) {
      $plugin_weight = $plugins_order[$plugin_id]['weight'];
      $sorted_plugins['plugin_labels'][$plugin_weight][$plugin_id] = $plugin_label;
    }

    ksort($sorted_plugins['plugin_labels']);

    foreach ($sorted_plugins['plugin_labels'] as $weight => $plugin_label) {
      $plugin_id = array_key_first($plugin_label);
      unset($sorted_plugins['plugin_labels'][$weight]);
      $sorted_plugins['plugin_labels'][$plugin_id] = $plugin_label[$plugin_id];
    }

    /* Sort plugin content */
    foreach ($plugin_content as $plugin_id => $plugin_content) {
      $plugin_weight = $plugins_order[$plugin_id]['weight'];
      $sorted_plugins['plugin_content'][$plugin_weight][$plugin_id] = $plugin_content;
    }

    ksort($sorted_plugins['plugin_content']);

    foreach ($sorted_plugins['plugin_content'] as $weight => $plugin_content) {
      $plugin_id = array_key_first($plugin_content);
      unset($sorted_plugins['plugin_content'][$weight]);
      $sorted_plugins['plugin_content'][$plugin_id] = $plugin_content[$plugin_id];
    }

    return $sorted_plugins;
  }

}
