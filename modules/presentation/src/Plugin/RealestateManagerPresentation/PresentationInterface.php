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
   */
  public function getLabel(): TranslatableMarkup;

  /**
   * Return presentation plugin content.
   */
  public function getContent(array $configuration, ?string $block_id): array;

}
