<?php

namespace Drupal\re_mgr_presentation\Manager;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides an Presentation plugin manager.
 */
class PresentationManager extends DefaultPluginManager {

  /**
   * Constructs a PresentationManager object.
   *
   * @phpstan-ignore-next-line
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/RealestateManagerPresentation',
      $namespaces,
      $module_handler,
      'Drupal\re_mgr_presentation\Plugin\RealestateManagerPresentation\PresentationInterface',
      'Drupal\re_mgr_presentation\Annotation\RealestateManagerPresentation'
    );
    $this->alterInfo('re_mgr_presentation');
    $this->setCacheBackend($cache_backend, 're_mgr_presentation_plugins');
  }

}
