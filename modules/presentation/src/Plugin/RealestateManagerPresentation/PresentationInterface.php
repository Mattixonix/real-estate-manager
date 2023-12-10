<?php

namespace Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the interface for presentation plugins.
 */
interface PresentationInterface extends ConfigurableInterface, PluginFormInterface {

  /**
   * Return presentation plugin label.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The label.
   */
  public function getLabel(): TranslatableMarkup;

  /**
   * Return presentation plugin content.
   *
   * @param array $configuration
   *   The block configuration.
   * @param string|null $block_id
   *   The block id.
   *
   * @return array
   *   Tabs content.
   */
  public function getContent(array $configuration, ?string $block_id): array;

}
